<?php

namespace App\Jobs;

use App\Models\WhatsappMessagesLog;
use App\Services\WaBlastSafetyService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendWhatsappMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Jumlah percobaan maksimal sebelum job masuk failed_jobs */
    public int $tries = 3;

    /** Backoff antar-percobaan dalam detik: percobaan ke-1=60s, ke-2=300s, ke-3=1800s */
    public array $backoff = [60, 300, 1800];

    /** Timeout eksekusi job (detik) — 5 menit cukup untuk delay + 1 request */
    public int $timeout = 300;

    private string $phone;
    private string $message;
    private int    $rencanaKerjaId;
    private ?int   $kepalaSekolahId = null;
    private ?int   $logId = null;

    public function __construct(
        string $phone,
        string $message,
        int    $rencanaKerjaId,
        ?int   $kepalaSekolahId = null,
        ?int   $logId = null
    ) {
        $this->phone           = $phone;
        $this->message         = $message;
        $this->rencanaKerjaId  = $rencanaKerjaId;
        $this->kepalaSekolahId = $kepalaSekolahId;
        $this->logId           = $logId;

        $this->onQueue('wa-blast');
    }

    public function handle()
    {
        // Cek apakah RencanaKerjaT masih ada di database
        if (!\App\Models\RencanaKerjaT::where('id', $this->rencanaKerjaId)->exists()) {
            Log::warning("[WA Job] Rencana Kerja ID {$this->rencanaKerjaId} sudah tidak ada di database. Job dibatalkan.");
            return;
        }

        // Jika queue connection sync (inline HTTP request), gunakan delay minimal 1-2s agar tidak HTTP 503 Timeout di Hostinger
        $isSync = config('queue.default') === 'sync';
        $delay  = $isSync ? rand(1, 2) : rand(10, 30);
        Log::info("[WA Job] Delay {$delay}s sebelum kirim ke {$this->phone} (Driver: " . config('queue.default') . ")");
        if ($delay > 0) {
            sleep($delay);
        }

        // Cek daily limit dari WaBlastSafetyService
        $safetyCheck = WaBlastSafetyService::checkCanSend();
        if (!$safetyCheck['allowed']) {
            Log::warning("[WA Job] Daily limit tercapai: " . $safetyCheck['message'] . ". Job di-release 30 menit.");
            $this->release(1800);
            return;
        }

        $token    = config('services.wablas.token');
        $secret   = config('services.wablas.secret');
        $endpoint = config('services.wablas.endpoint', 'https://jogja.wablas.com/api/send-message');

        $rawPhone = preg_replace('/^(\+?62|0)/', '', $this->phone);

        $logEntry = null;
        if (isset($this->logId) && $this->logId) {
            $logEntry = WhatsappMessagesLog::find($this->logId);
        }

        if (!$logEntry) {
            $logQuery = WhatsappMessagesLog::where('rencana_kerja_id', $this->rencanaKerjaId)
                ->where(function($q) use ($rawPhone) {
                    $q->where('phone_number', 'LIKE', '%' . $rawPhone)
                      ->orWhere('phone_number', $this->phone);
                });
            if (isset($this->kepalaSekolahId) && !empty($this->kepalaSekolahId)) {
                $logQuery->where('kepala_sekolah_id', $this->kepalaSekolahId);
            }
            $logEntry = $logQuery->latest()->first();
        }

        if (!$logEntry) {
            $logEntry = new WhatsappMessagesLog();
            $logEntry->rencana_kerja_id  = $this->rencanaKerjaId;
            $logEntry->kepala_sekolah_id = isset($this->kepalaSekolahId) ? $this->kepalaSekolahId : null;
            $logEntry->phone_number      = $this->phone;
        }

        $logEntry->message  = $this->message;
        $logEntry->job_uuid = $this->job ? $this->job->uuid() : null;

        try {
            $isWatBiz   = str_contains($endpoint, 'watbiz.com');
            $isDikontak = str_contains($endpoint, 'dikontak.com');

            if ($isWatBiz) {
                // WatBiz API
                $response = Http::withHeaders([
                    'Api-key' => $token,
                ])
                ->asJson()
                ->timeout(30)
                ->post($endpoint, [
                    'number'    => $this->phone,
                    'message'   => $this->message,
                    'device_id' => (int) config('services.wablas.device_id', 0),
                ]);
            } elseif ($isDikontak) {
                // Dikontak API (WABA Shared / Dedicated) — membutuhkan JSON body
                $authHeader = WaBlastSafetyService::getWorkingAuthFormat($token, $secret, $endpoint);
                $response = Http::withHeaders(['Authorization' => $authHeader])
                    ->asJson()
                    ->timeout(30)
                    ->post($endpoint, [
                        'phone'   => $this->phone,
                        'message' => $this->message,
                    ]);
            } else {
                // Legacy Wablas API
                $authHeader = WaBlastSafetyService::getWorkingAuthFormat($token, $secret, $endpoint);
                $response = Http::withHeaders(['Authorization' => $authHeader])
                    ->asForm()
                    ->timeout(30)
                    ->post($endpoint, [
                        'phone'   => $this->phone,
                        'message' => $this->message,
                    ]);
            }

            $body   = $response->body();
            $resArr = json_decode($body, true);

            // Deteksi Error 463 / Rate Overlimit
            if ($this->isRateOverlimit($response->status(), $body)) {
                Log::warning("[WA Job] Error 463 / Rate Overlimit ke {$this->phone}. Job di-pause 30 menit.");
                $logEntry->is_sent        = false;
                $logEntry->failure_reason = 'Rate Overlimit (463) – job dijadwal ulang 30 menit';
                $logEntry->save();

                if (!$isWatBiz) {
                    WaBlastSafetyService::clearAuthCache();
                }
                $this->release(1800);
                return;
            }

            $statusVal = $resArr['status'] ?? '';
            $isSuccess = $response->successful() && ($statusVal === true || $statusVal === 'success' || (is_array($resArr) && $statusVal !== false && $statusVal !== 'error'));

            if ($isSuccess) {
                $logEntry->is_sent        = true;
                $logEntry->failure_reason = null;
                $logEntry->save();

                // Update semua variasi log untuk rencana_kerja & nomor HP ini (misal 08x vs 62x)
                if (!empty($rawPhone)) {
                    WhatsappMessagesLog::where('rencana_kerja_id', $this->rencanaKerjaId)
                        ->where('phone_number', 'LIKE', '%' . $rawPhone)
                        ->update([
                            'is_sent'        => true,
                            'failure_reason' => null,
                        ]);
                }

                Log::info("[WA Job] Berhasil kirim ke {$this->phone}");

                // Update status pada rencana_kerja_t menjadi 1 (Sudah Kirim WA Blast)
                \App\Models\RencanaKerjaT::where('id', $this->rencanaKerjaId)->update(['status' => 1]);
                return;
            }

            // Gagal tapi bukan rate limit — tandai failure, biarkan retry normal
            $errMsg = $resArr['message'] ?? (str_contains($body, '<html') ? 'HTTP Error ' . $response->status() . ' (HTML 404/500)' : $body);
            if (str_contains($body, 'IP')) {
                $errMsg .= ' (IP Server belum di-whitelist di Wablas)';
            }

            $logEntry->is_sent        = false;
            $logEntry->failure_reason = substr($errMsg, 0, 250);
            $logEntry->save();

            throw new \Exception("Wablas response gagal: {$errMsg}");

        } catch (\Exception $e) {
            if (empty($logEntry->failure_reason)) {
                $logEntry->failure_reason = substr($e->getMessage(), 0, 250);
                $logEntry->is_sent        = false;
                $logEntry->save();
            }

            Log::error("[WA Job] Exception kirim ke {$this->phone}: " . $e->getMessage());
            throw $e; // Re-throw agar queue runner bisa retry sesuai $backoff
        }
    }

    /**
     * Dipanggil setelah semua $tries habis — job masuk failed_jobs.
     */
    public function failed(\Throwable $exception)
    {
        Log::error("[WA Job] Job PERMANENTLY FAILED ke {$this->phone}: " . $exception->getMessage());

        try {
            $rkId = isset($this->rencanaKerjaId) ? $this->rencanaKerjaId : null;
            if ($rkId && \App\Models\RencanaKerjaT::where('id', $rkId)->exists()) {
                WhatsappMessagesLog::updateOrCreate(
                    [
                        'rencana_kerja_id'  => $rkId,
                        'kepala_sekolah_id' => isset($this->kepalaSekolahId) ? $this->kepalaSekolahId : null,
                        'phone_number'      => $this->phone,
                    ],
                    [
                        'message'        => $this->message,
                        'is_sent'        => false,
                        'failure_reason' => substr('Job gagal permanen setelah ' . $this->tries . ' percobaan: ' . $exception->getMessage(), 0, 250),
                    ]
                );
            }
        } catch (\Throwable $e) {
            Log::error("[WA Job failed handler] Tidak dapat menyimpan ke whatsapp_messages_log: " . $e->getMessage());
        }
    }

    /**
     * Deteksi response Error 463 atau HTTP 429/503 (rate limiting).
     */
    private function isRateOverlimit(int $httpStatus, string $body): bool
    {
        if (in_array($httpStatus, [429, 503])) {
            return true;
        }

        $lower = strtolower($body);
        return str_contains($lower, '463')
            || str_contains($lower, 'rate overlimit')
            || str_contains($lower, 'rate limit')
            || str_contains($lower, 'too many');
    }
}
