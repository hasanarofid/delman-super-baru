<?php

namespace App\Services;

use App\Models\WaBlastSetting;
use App\Models\WhatsappMessagesLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class WaBlastSafetyService
{
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

        // Anti-Spam Suffix
        if (filter_var(env('WABLAS_ANTI_SPAM_SUFFIX', true), FILTER_VALIDATE_BOOLEAN)) {
            $message .= "\n\n[Ref: " . date('YmdHis') . "-" . rand(100, 999) . "]";
        }

        return $message;
    }
}
