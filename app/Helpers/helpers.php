<?php

if (! function_exists('wa_link')) {
    /**
     * Format a phone number into a WhatsApp API URL.
     * Strips non-digits, converts leading 0 to country code 62 (Indonesia).
     *
     * @param  string  $phone
     * @return string  https://wa.me/{number}
     */
    function wa_link(string $phone): string
    {
        $num = preg_replace('/\D/', '', $phone);

        if (str_starts_with($num, '0')) {
            $num = '62' . substr($num, 1);
        } elseif (! str_starts_with($num, '62')) {
            $num = '62' . $num;
        }

        return 'https://wa.me/' . $num;
    }
}
