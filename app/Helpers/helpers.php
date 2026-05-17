<?php

if (! function_exists('format_phone_number')) {
    /**
     * Format a phone number to Indonesian international format (without leading +).
     * Strips non-digits, converts leading 0 to country code 62 (Indonesia).
     *
     * @param  string|null  $phone
     * @return string  e.g. 6281234567890
     */
    function format_phone_number(?string $phone): string
    {
        if (! $phone) {
            return '';
        }

        $num = preg_replace('/\D/', '', $phone);

        if (str_starts_with($num, '0')) {
            $num = '62' . substr($num, 1);
        } elseif (! str_starts_with($num, '62')) {
            $num = '62' . $num;
        }

        return $num;
    }
}

if (! function_exists('format_tanggal_id')) {
    /**
     * Format a date into Indonesian month name format.
     * Accepts a date string or Carbon instance.
     *
     * @param  string|\Carbon\Carbon|null  $date
     * @param  string  $format  Output format: 'd/M/Y' by default (e.g. 15/Mei/2026)
     * @return string
     */
    function format_tanggal_id($date, string $separator = '/'): string
    {
        if (! $date) {
            return '-';
        }

        $bulan = [
            1  => 'Jan',  2  => 'Feb', 3  => 'Mar',
            4  => 'Apr',  5  => 'Mei', 6  => 'Jun',
            7  => 'Jul',  8  => 'Agu', 9  => 'Sep',
            10 => 'Okt',  11 => 'Nov', 12 => 'Des',
        ];

        $carbon = $date instanceof \Carbon\Carbon
            ? $date
            : \Carbon\Carbon::parse($date);

        return $carbon->day . $separator . $bulan[$carbon->month] . $separator . $carbon->year;
    }
}

if (! function_exists('wa_link')) {
    /**
     * Generate a WhatsApp API URL from a phone number.
     *
     * @param  string|null  $phone
     * @return string  https://wa.me/{number} or '#' if phone is empty
     */
    function wa_link(?string $phone): string
    {
        $formatted = format_phone_number($phone);

        if (! $formatted) {
            return '#';
        }

        return 'https://wa.me/' . $formatted;
    }
}
