<?php

namespace App\Helpers;

class CountryHelper
{
    public static function countryToEmoji(string $code): string
    {
        $code = strtoupper($code);
        if (strlen($code) !== 2) return '🏳️';

        return iconv(
            'UTF-8',
            'UTF-8//IGNORE',
            mb_chr(0x1F1E6 + ord($code[0]) - ord('A'), 'UTF-8') .
                mb_chr(0x1F1E6 + ord($code[1]) - ord('A'), 'UTF-8')
        );
    }
}
