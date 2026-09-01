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

        $token    = (string) (config('services.wablas.token') ?? '');
        $secret   = (string) (config('services.wablas.secret') ?? '');
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
                // Dikontak API (WABA Shared / Dedicated / Text)
                $authHeader = WaBlastSafetyService::getWorkingAuthFormat($token, $secret, $endpoint);
                
                $isWabaTemplate = str_contains($endpoint, '/waba/message') || str_contains($endpoint, '/waba-shared/message');
                if ($isWabaTemplate) {
                    // Deteksi jenis pesan: Untuk Pengawas vs Untuk Kepala Sekolah / Guru
                    $isPengawasMsg = str_contains($this->message, 'Rencana Kerja Mandiri') || str_contains($this->message, 'refleksi mandiri');
                    if ($isPengawasMsg) {
                        // Template umpan_balik_pengawas_baru (2052379852150869) - 5 variabel | umpan_balik_pengawas_new (2245694162953337) - 6 variabel | Legacy (1779175166415538) - 4 variabel
                        $templateId = config('services.wablas.template_id_pengawas', '2052379852150869');
                        
                        $p1 = 'Pengawas';
                        $p2 = '-';
                        $p3 = 'https://delmansuper.id';
                        $p4 = date('YmdHis');

                        if (preg_match('/Halo Bapak\/Ibu\s+\*?(.*?)\*?,\s*Anda telah membuat/i', $this->message, $m1)) {
                            $p1 = trim($m1[1], " *\t\n\r\0\x0B");
                        }
                        if (preg_match('/Rencana Kerja Mandiri:\s+\*?(.*?)\*?\.\s*Silakan isi/i', $this->message, $m2)) {
                            $p2 = trim($m2[1], " *\t\n\r\0\x0B");
                        }
                        if (preg_match('/(https?:\/\/[^\s_\*]+)/i', $this->message, $m3)) {
                            $p3 = trim($m3[1], " *\t\n\r\0\x0B");
                        }
                        if (preg_match('/ref:\s*([^\s\.\_\*]+)/i', $this->message, $m4)) {
                            $p4 = trim($m4[1], " ._\t\n\r\0\x0B");
                        }

                        if ($templateId === '2052379852150869' || $templateId === 'umpan_balik_pengawas_baru') {
                            // 5 Variabel untuk umpan_balik_pengawas_baru (Header {{1}} + Body {{2..5}} tanpa tombol URL)
                            $headerP1 = mb_strimwidth((string) $p1, 0, 25, '');

                            $paramList = [
                                (string) $headerP1, // Header {{1}}: nama pengawas (max 25 chars agar total header < 60 chars)
                                (string) $p1,       // Body {{2}}: nama pengawas
                                (string) $p2,       // Body {{3}}: rencana kerja
                                (string) $p3,       // Body {{4}}: link umpan balik
                                (string) $p4        // Body {{5}}: ref
                            ];
                        } elseif ($templateId === '2245694162953337' || $templateId === 'umpan_balik_pengawas_new') {
                            // Meta WABA Header max 60 chars ("Rencana Pengembangan Kompetensi " = 33 chars -> max var = 25 chars)
                            $headerP1 = mb_strimwidth((string) $p1, 0, 25, '');

                            // 6 Variabel untuk umpan_balik_pengawas_new (Header {{1}} + Body {{2..5}} + Button URL Suffix {{6}})
                            $paramList = [
                                (string) $headerP1, // Header {{1}}: nama pengawas
                                (string) $p1,       // Body {{2}}: nama pengawas
                                (string) $p2,       // Body {{3}}: rencana kerja
                                (string) $p3,       // Body {{4}}: link umpan balik
                                (string) $p4,       // Body {{5}}: ref
                                (string) $p3        // Button URL suffix {{6}}
                            ];
                        } else {
                            // 4 Variabel untuk template lama 1779175166415538
                            $paramList = [(string) $p1, (string) $p2, (string) $p3, (string) $p4];
                        }
                    } else {
                        // Template umpan_balik_kepsek_baru (1379915427092136) - 9 variabel | umpan_balik_kepsek_new (1013364065068172) - 10 variabel | Legacy (1588095976123570) - 7 variabel
                        $templateId = config('services.wablas.template_id', '1379915427092136');

                        $p1 = 'Bapak/Ibu';
                        $p2 = 'Sekolah';
                        $p3 = date('F');
                        $p4 = date('Y');
                        $p5 = 'Pengawas';
                        $p6 = 'Pengawasan';
                        $p7 = 'https://delmansuper.id';
                        $p8 = date('YmdHis');

                        if (preg_match('/Yth Bapak \/ Ibu\s+\*?(.*?)\*?\s+Kepala/i', $this->message, $m1)) {
                            $p1 = trim($m1[1], " *\t\n\r\0\x0B");
                        }
                        if (preg_match('/Kepala\s+\*?(.*?)\*?,\s*Pada bulan/i', $this->message, $m2)) {
                            $p2 = trim($m2[1], " *\t\n\r\0\x0B");
                        }
                        if (preg_match('/Pada bulan\s+\*?(.*?)\*?\s+\*?[0-9]/i', $this->message, $m3)) {
                            $p3 = trim($m3[1], " *\t\n\r\0\x0B");
                        }
                        if (preg_match('/Pada bulan.*?\s+\*?([0-9]{4}(?:\/[0-9]{4})?)\*?\s+pengawas/i', $this->message, $m4)) {
                            $p4 = trim($m4[1], " *\t\n\r\0\x0B");
                        }
                        if (preg_match('/pengawas\s+\*?(.*?)\*?\s+akan melakukan/i', $this->message, $m5)) {
                            $p5 = trim($m5[1], " *\t\n\r\0\x0B");
                        }
                        if (preg_match('/kegiatan pengawasan\s+\*?(.*?)\*?\s+ke sekolah/i', $this->message, $m6)) {
                            $p6 = trim($m6[1], " *\t\n\r\0\x0B");
                        }
                        if (preg_match('/(https?:\/\/[^\s\*]+)/i', $this->message, $m7)) {
                            $p7 = trim($m7[1], " *\t\n\r\0\x0B");
                        }
                        if (preg_match('/ref:\s*([^\s\.\_\*]+)/i', $this->message, $m8)) {
                            $p8 = trim($m8[1], " ._\t\n\r\0\x0B");
                        }

                        if ($templateId === '1379915427092136' || $templateId === 'umpan_balik_kepsek_baru') {
                            // 9 Variabel untuk umpan_balik_kepsek_baru (Header {{1}} + Body {{2..9}} tanpa tombol URL)
                            $headerP5 = mb_strimwidth((string) $p5, 0, 35, '');

                            $paramList = [
                                (string) $headerP5, // Header {{1}}: nama pengawas (max 35 chars agar total header < 60 chars)
                                (string) $p1,       // Body {{2}}: nama kepsek/guru
                                (string) $p2,       // Body {{3}}: nama sekolah
                                (string) $p3,       // Body {{4}}: bulan
                                (string) $p4,       // Body {{5}}: tahun
                                (string) $p5,       // Body {{6}}: nama pengawas
                                (string) $p6,       // Body {{7}}: nama rencana kerja
                                (string) $p7,       // Body {{8}}: link umpan balik
                                (string) $p8        // Body {{9}}: ref
                            ];
                        } elseif ($templateId === '1013364065068172' || $templateId === '1913364065068172' || $templateId === 'umpan_balik_kepsek_new') {
                            // Meta WABA Header max 60 chars ("Rencana Pengawasan " = 19 chars -> max var = 35 chars)
                            $headerP5 = mb_strimwidth((string) $p5, 0, 35, '');

                            // 10 Variabel untuk umpan_balik_kepsek_new (Header {{1}} + Body {{2..9}} + Button URL Suffix {{10}})
                            $paramList = [
                                (string) $headerP5, // Header {{1}}: nama pengawas (max 35 chars agar total header < 60 chars)
                                (string) $p1,       // Body {{2}}: nama kepsek/guru
                                (string) $p2,       // Body {{3}}: nama sekolah
                                (string) $p3,       // Body {{4}}: bulan
                                (string) $p4,       // Body {{5}}: tahun
                                (string) $p5,       // Body {{6}}: nama pengawas
                                (string) $p6,       // Body {{7}}: nama rencana kerja
                                (string) $p7,       // Body {{8}}: link umpan balik
                                (string) $p8,       // Body {{9}}: ref
                                (string) $p7        // Button URL suffix {{10}}
                            ];
                        } else {
                            // 7 Variabel untuk template lama 1588095976123570
                            $paramList = [(string) $p1, (string) $p2, (string) $p3, (string) $p4, (string) $p5, (string) $p6, (string) $p7];
                        }
                    }

                    // Build WABA variable payload formats
                    $indexedVars = array_values($paramList);
                    
                    $format1Obj = [];
                    foreach ($indexedVars as $idx => $val) {
                        $key = (string) ($idx + 1);
                        $format1Obj[$key] = (string) $val; // {"1": "val1", "2": "val2", ...}
                    }

                    if ($isPengawasMsg) {
                        $namedVars = [
                            'nama_pengawas'    => (string) $p1,
                            'rencana_kerja'    => (string) $p2,
                            'link_umpan_balik' => (string) $p3,
                            'ref'              => (string) $p4,
                        ];
                    } else {
                        $namedVars = [
                            'nama_guru'          => (string) $p1,
                            'nama_sekolah'       => (string) $p2,
                            'bulan'              => (string) $p3,
                            'tahun'              => (string) $p4,
                            'nama_pengawas'      => (string) $p5,
                            'nama_rencana_kerja' => (string) $p6,
                            'link_umpan_balik'   => (string) $p7,
                        ];
                    }

                    $textParamObjects = array_map(function($v) {
                        return ['type' => 'text', 'text' => (string) $v];
                    }, $indexedVars);

                    $payloadFormats = [
                        // Format #1 (Resmi Sesuai Dokumentasi Dikontak): Indexed array ["val1", "val2", ...]
                        ['template_id' => (string) $templateId, 'phone' => $this->phone, 'variables' => $indexedVars],
                        // Format #2: Named keys object {"nama_guru": "val1", ...}
                        ['template_id' => (string) $templateId, 'phone' => $this->phone, 'variables' => $namedVars],
                        // Format #3: Pure 3-key payload with 1-indexed object {"1": "val1", "2": "val2", ...}
                        ['template_id' => (string) $templateId, 'phone' => $this->phone, 'variables' => $format1Obj],
                        // Format #4: Meta WABA parameters array [{"type": "text", "text": "val1"}, ...]
                        ['template_id' => (string) $templateId, 'phone' => $this->phone, 'parameters' => $textParamObjects],
                        // Format #5: Meta WABA variables array with text objects
                        ['template_id' => (string) $templateId, 'phone' => $this->phone, 'variables' => $textParamObjects],
                        // Format #6: Meta WABA components format
                        ['template_id' => (string) $templateId, 'phone' => $this->phone, 'components' => [['type' => 'body', 'parameters' => $textParamObjects]]],
                    ];

                    $response = null;
                    $isSuccess = false;
                    foreach ($payloadFormats as $fIndex => $payload) {
                        Log::info("[WA Job] Trying WABA Payload Format #" . ($fIndex + 1) . " to {$endpoint}:", $payload);
                        $res = Http::withHeaders(['Authorization' => $authHeader])
                            ->asJson()
                            ->timeout(30)
                            ->post($endpoint, $payload);

                        $body = $res->body();
                        Log::info("[WA Job] WABA Format #" . ($fIndex + 1) . " Response (HTTP {$res->status()}): " . $body);

                        $resData = json_decode($body, true);
                        if ($res->successful() && isset($resData['status']) && ($resData['status'] === true || $resData['status'] === 'true' || $resData['status'] === 1)) {
                            Log::info("[WA Job] WABA SUCCESS using Format #" . ($fIndex + 1));
                            $response = $res;
                            $isSuccess = true;
                            break;
                        }

                        $response = $res;
                    }

                    // Automatic fallback to standard Dikontak send-message API if WABA template is rejected by Meta
                    if (!$isSuccess) {
                        $fallbackEndpoint = 'https://dikontak.com/api/v3/send-message';
                        Log::warning("[WA Job] WABA Template API failed. Falling back to standard send-message API: {$fallbackEndpoint}");
                        
                        // Try asForm first for /send-message
                        $resFallback = Http::withHeaders(['Authorization' => $authHeader])
                            ->asForm()
                            ->timeout(30)
                            ->post($fallbackEndpoint, [
                                'phone'   => $this->phone,
                                'message' => $this->message,
                            ]);

                        if (!$resFallback->successful() || (isset(json_decode($resFallback->body(), true)['status']) && json_decode($resFallback->body(), true)['status'] === false)) {
                            Log::info("[WA Job] Retrying Fallback using asJson...");
                            $resFallback = Http::withHeaders(['Authorization' => $authHeader])
                                ->asJson()
                                ->timeout(30)
                                ->post($fallbackEndpoint, [
                                    'phone'   => $this->phone,
                                    'message' => $this->message,
                                ]);
                        }

                        Log::info("[WA Job] Standard Fallback Response (HTTP {$resFallback->status()}): " . $resFallback->body());
                        $resDataFb = json_decode($resFallback->body(), true);
                        if ($resFallback->successful() && isset($resDataFb['status']) && ($resDataFb['status'] === true || $resDataFb['status'] === 'true' || $resDataFb['status'] === 1)) {
                            Log::info("[WA Job] SUCCESS via Standard Fallback Endpoint");
                            $response = $resFallback;
                        } else {
                            $response = $resFallback;
                        }
                    }
                } else {
                    $payload = [
                        'phone'   => $this->phone,
                        'message' => $this->message,
                    ];
                    $response = Http::withHeaders(['Authorization' => $authHeader])
                        ->asJson()
                        ->timeout(30)
                        ->post($endpoint, $payload);

                    if (!$response->successful() || (isset(json_decode($response->body(), true)['status']) && json_decode($response->body(), true)['status'] === false)) {
                        $response = Http::withHeaders(['Authorization' => $authHeader])
                            ->asForm()
                            ->timeout(30)
                            ->post($endpoint, $payload);
                    }
                }
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
