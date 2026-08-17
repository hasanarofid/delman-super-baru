<?php

namespace App\Services;

use App\Helpers\SpintaxHelper;
use App\Models\WaBlastSetting;
use App\Models\WhatsappMessagesLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class WaBlastSafetyService
{
    /** Cache key untuk format auth Wablas yang berhasil */
    private const AUTH_CACHE_KEY = 'wablas_auth_format';

    /** Durasi cache auth dalam menit (6 jam) */
    private const AUTH_CACHE_MINUTES = 360;
    /**
     * Check if a message can be sent based on daily limits & warming-up rules.
     *
     * @return array
     */
    public static function checkCanSend()
    {
        $reconnectDate = Carbon::today();
        $day1_3Limit = 30;
        $day4_7Limit = 70;
        $stableLimit = 200;
        $status = 'warmup';

        // Check if database table exists
        if (Schema::hasTable('wa_blast_settings')) {
            $setting = WaBlastSetting::first();
            if (!$setting) {
                $setting = WaBlastSetting::create([
                    'reconnect_date' => Carbon::today()->toDateString(),
                    'status' => 'warmup',
                    'day1_3_limit' => 30,
                    'day4_7_limit' => 70,
                    'stable_limit' => 200,
                ]);
            }
            $reconnectDate = Carbon::parse($setting->reconnect_date);
            $day1_3Limit = (int) $setting->day1_3_limit;
            $day4_7Limit = (int) $setting->day4_7_limit;
            $stableLimit = (int) $setting->stable_limit;
            $status = $setting->status;
        }

        // Calculate days since reconnect (Day 1, Day 2, etc.)
        $daysCount = max(1, (int) $reconnectDate->diffInDays(Carbon::today()) + 1);

        // Determine phase, daily limit, and delay range
        if ($status === 'stable' || $daysCount > 7) {
            $phaseName = "Fase Stabil";
            $dailyLimit = $stableLimit;
            $minDelay = 10;
            $maxDelay = (int) (config('services.wablas.delay') ?: env('WABLAS_DELAY_SECONDS', 15));
            $minDelay = min($minDelay, $maxDelay);
        } elseif ($daysCount <= 3) {
            $phaseName = "Warming-Up (Hari ke-{$daysCount})";
            $dailyLimit = $day1_3Limit;
            $minDelay = 20;
            $maxDelay = 30;
        } else {
            $phaseName = "Warming-Up (Hari ke-{$daysCount})";
            $dailyLimit = $day4_7Limit;
            $minDelay = 15;
            $maxDelay = 25;
        }

        // Count messages sent today
        $sentToday = 0;
        if (Schema::hasTable('whatsapp_messages_log')) {
            $sentToday = WhatsappMessagesLog::whereDate('created_at', Carbon::today())
                ->where('is_sent', true)
                ->count();
        }

        $allowed = ($sentToday < $dailyLimit);
        $delaySeconds = rand($minDelay, max($minDelay, $maxDelay));

        return [
            'allowed' => $allowed,
            'sent_today' => $sentToday,
            'daily_limit' => $dailyLimit,
            'days_count' => $daysCount,
            'phase_name' => $phaseName,
            'delay_seconds' => $delaySeconds,
            'message' => $allowed 
                ? "Pengiriman diizinkan ({$sentToday}/{$dailyLimit} pesan hari ini)"
                : "Batas pengiriman harian masa pemulihan tercapai ({$sentToday}/{$dailyLimit} pesan). Pengiriman dihentikan demi menjaga akun WA dari banned Meta."
        ];
    }

    /**
     * Apply randomized delay based on current warming-up phase.
     */
    public static function applySafetyDelay()
    {
        $status = self::checkCanSend();
        $delay = $status['delay_seconds'] ?? 10;
        if ($delay > 0) {
            sleep($delay);
        }
        return $delay;
    }

    /**
     * Format message body with anti-spam random suffix.
     *
     * @param string $message
     * @return string
     */
    public static function prepareMessageBody($message)
    {
        // Branding Replacement
        $message = str_ireplace(
            ['simodip', 'sistem modip', 'Sistem Monitoring dan Evaluasi Digital Pengawas'],
            'DelmanSuper',
            $message
        );

        // Spintax: variasi pesan agar tidak identik antar-penerima
        $message = SpintaxHelper::parse($message);

        // Anti-Spam Suffix
        if (filter_var(config('services.wablas.anti_spam_suffix', true), FILTER_VALIDATE_BOOLEAN)) {
            $message .= "\n\n[Ref: " . date('YmdHis') . "-" . rand(100, 999) . "]";
        }

        return $message;
    }

    /**
     * Dapatkan format auth Wablas yang berhasil (cache 6 jam).
     * Menghilangkan percobaan 3x HTTP setiap pengiriman.
     */
    public static function getWorkingAuthFormat(string $token, string $secret, string $endpoint): string
    {
        $cached = Cache::get(self::AUTH_CACHE_KEY);
        if ($cached) {
            return $cached;
        }

        // Daftar format auth (urut dari Bearer, token plain, dan token.secret)
        $formats = array_filter([
            'bearer'           => "Bearer {$token}",
            'token_only'       => $token,
            'token_dot_secret' => !empty($secret) ? "{$token}.{$secret}" : null,
        ]);

        $isDikontak = str_contains($endpoint, 'dikontak.com');

        foreach ($formats as $name => $authHeader) {
            try {
                $req = Http::withHeaders(['Authorization' => $authHeader]);
                if ($isDikontak) {
                    $req = $req->asJson();
                } else {
                    $req = $req->asForm();
                }

                $response = $req->timeout(15)->post($endpoint, [
                    'phone'   => '6200000000000', // Dummy — tidak terkirim
                    'message' => 'ping',
                ]);

                $body = $response->body();
                // Hanya cache format jika response HTTP 2xx dan bukan 'invalid request body'
                if ($response->successful() && !str_contains($body, 'invalid request body')) {
                    Cache::put(self::AUTH_CACHE_KEY, $authHeader, now()->addMinutes(self::AUTH_CACHE_MINUTES));
                    Log::info("[WA Auth] Format '{$name}' berhasil, di-cache {" . self::AUTH_CACHE_MINUTES . "} menit.");
                    return $authHeader;
                }
            } catch (\Exception $e) {
                Log::warning("[WA Auth] Format '{$name}' gagal: " . $e->getMessage());
            }
        }

        // Fallback: pakai Bearer atau token.secret
        $fallback = !empty($secret) ? "{$token}.{$secret}" : "Bearer {$token}";
        Cache::put(self::AUTH_CACHE_KEY, $fallback, now()->addMinutes(30));
        return $fallback;
    }

    /**
     * Hapus cache auth format (dipanggil saat Error 463 / auth berubah).
     */
    public static function clearAuthCache(): void
    {
        Cache::forget(self::AUTH_CACHE_KEY);
    }

    /**
     * Validasi nomor telepon Indonesia.
     * Panjang setelah prefix 62: 10-14 digit (total 12-16 digit).
     *
     * @return array ['valid' => bool, 'phone' => string, 'reason' => string|null]
     */
    public static function validatePhoneNumber(string $phone): array
    {
        $phone = self::formatPhoneNumber($phone);

        $digitOnly = preg_replace('/[^0-9]/', '', $phone);
        $length    = strlen($digitOnly);

        if ($length < 12 || $length > 15) {
            return [
                'valid'  => false,
                'phone'  => $digitOnly,
                'reason' => "Panjang nomor tidak valid ({$length} digit, harus 12-15 setelah prefix 62)",
            ];
        }

        if (substr($digitOnly, 0, 2) !== '62') {
            return [
                'valid'  => false,
                'phone'  => $digitOnly,
                'reason' => "Nomor tidak diawali prefix 62",
            ];
        }

        return ['valid' => true, 'phone' => $digitOnly, 'reason' => null];
    }

    /**
     * Format nomor telepon ke prefix 62.
     */
    public static function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
