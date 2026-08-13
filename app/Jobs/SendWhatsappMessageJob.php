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
    private ?int   $kepalaSekolahId;

    public function __construct(
        string $phone,
        string $message,
        int    $rencanaKerjaId,
        ?int   $kepalaSekolahId = null
    ) {
        $this->phone           = $phone;
        $this->message         = $message;
        $this->rencanaKerjaId  = $rencanaKerjaId;
        $this->kepalaSekolahId = $kepalaSekolahId;

        $this->onQueue('wa-blast');
    }

    public function handle()
    {
        // Delay acak 30–90 detik agar tidak burst ke Wablas
        $delay = rand(30, 90);
        Log::info("[WA Job] Delay {$delay}s sebelum kirim ke {$this->phone}");
        sleep($delay);

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

        // Gunakan auth format yang sudah di-cache, atau deteksi otomatis
        $authHeader = WaBlastSafetyService::getWorkingAuthFormat($token, $secret, $endpoint);

        $logEntry                  = new WhatsappMessagesLog();
        $logEntry->rencana_kerja_id = $this->rencanaKerjaId;
        $logEntry->kepala_sekolah_id = $this->kepalaSekolahId;
        $logEntry->phone_number    = $this->phone;
        $logEntry->message         = $this->message;
        $logEntry->job_uuid        = $this->job ? $this->job->uuid() : null;

        try {
            $response = Http::withHeaders(['Authorization' => $authHeader])
                ->asForm()
                ->timeout(30)
                ->post($endpoint, [
                    'phone'   => $this->phone,
                    'message' => $this->message,
                ]);

            $body   = $response->body();
            $resArr = json_decode($body, true);

            // Deteksi Error 463 (Rate Overlimit Wablas)
            if ($this->isRateOverlimit($response->status(), $body)) {
                Log::warning("[WA Job] Error 463 / Rate Overlimit ke {$this->phone}. Job di-pause 30 menit.");
                $logEntry->is_sent        = false;
                $logEntry->failure_reason = 'Rate Overlimit (463) – job dijadwal ulang 30 menit';
                $logEntry->save();

                // Invalidasi cache auth agar di-refresh saat retry
                WaBlastSafetyService::clearAuthCache();
                $this->release(1800);
                return;
            }

            if ($response->successful() && ($resArr['status'] ?? '') !== false) {
                $logEntry->is_sent = true;
                $logEntry->save();
                Log::info("[WA Job] Berhasil kirim ke {$this->phone}");

                // Update status pada rencana_kerja_t menjadi 1 (Sudah Kirim WA Blast)
                \App\Models\RencanaKerjaT::where('id', $this->rencanaKerjaId)->update(['status' => 1]);
                return;
            }

            // Gagal tapi bukan rate limit — tandai failure, biarkan retry normal
            $errMsg = $resArr['message'] ?? $body;
            if (str_contains($body, 'IP')) {
                $errMsg .= ' (IP Server belum di-whitelist di Wablas)';
            }

            $logEntry->is_sent        = false;
            $logEntry->failure_reason = $errMsg;
            $logEntry->save();

            throw new \Exception("Wablas response gagal: {$errMsg}");

        } catch (\Exception $e) {
            if (empty($logEntry->failure_reason)) {
                $logEntry->failure_reason = $e->getMessage();
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

        WhatsappMessagesLog::updateOrCreate(
            [
                'rencana_kerja_id'  => $this->rencanaKerjaId,
                'kepala_sekolah_id' => $this->kepalaSekolahId,
                'phone_number'      => $this->phone,
            ],
            [
                'is_sent'        => false,
                'failure_reason' => 'Job gagal permanen setelah ' . $this->tries . ' percobaan: ' . $exception->getMessage(),
            ]
        );
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
