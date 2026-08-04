<?php

namespace App\Helpers;

class SpintaxHelper
{
    /**
     * Parse spintax string dan kembalikan variasi acak.
     *
     * Format: {opsi1|opsi2|opsi3}
     * Mendukung nested spintax: {Halo {Pak|Bu}|Selamat pagi}
     *
     * @param string $text
     * @return string
     */
    public static function parse(string $text): string
    {
        while (preg_match('/\{([^{}]+)\}/', $text, $matches)) {
            $options = explode('|', $matches[1]);
            $chosen  = trim($options[array_rand($options)]);
            $text    = str_replace($matches[0], $chosen, $text);
        }

        return $text;
    }

    /**
     * Salam pembuka acak untuk WA Blast (Bahasa Indonesia formal).
     *
     * @return string
     */
    public static function randomGreeting(): string
    {
        return self::parse(
            '{Yth.|Yang terhormat} {Bapak/Ibu|Bapak atau Ibu}'
        );
    }

    /**
     * Salam penutup acak.
     *
     * @return string
     */
    public static function randomClosing(): string
    {
        return self::parse(
            '{Terima kasih atas perhatian dan kerja samanya.|Kami menghaturkan terima kasih.|Terima kasih.}'
        );
    }
}
