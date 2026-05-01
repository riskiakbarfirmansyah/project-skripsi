<?php

namespace App\Services;

use App\Models\narasumberbyphone;
use App\Models\narasumbertalkshow;

class JadwalValidationService
{
    /**
     * 11 valid schedule keys.
     */
    public const SCHEDULES = [
        'pendidikan_kota_kita' => 'Pendidikan Kota Kita',
        'youth_speak_up'       => 'Youth Speak Up',
        'layanan_publik'       => 'Layanan Publik',
        'anda_dan_keluarga'    => 'Anda dan Keluarga',
        'dinkes_menyapa'       => 'Dinkes Menyapa',
        'komunitas_bicara'     => 'Komunitas Bicara',
        'indie_vibes'          => 'Indie Vibes',
        'kojah_sastra'         => 'Kojah Sastra',
        'inspirasi_bisnis'     => 'Inspirasi Bisnis',
        'obrolan_agama_islam'  => 'Obrolan Seputar Agama Islam',
        'ngosasi'              => 'Ngosasi (Ngobrol Asik Sana Sini)',
    ];

    /**
     * Check whether the given JAM (HH:MM) on a specific TANGGAL
     * is not already used by another scheduled talkshow narasumber.
     *
     * Same time on a different day is ALLOWED.
     * Same time on the SAME day is BLOCKED.
     *
     * @param  string      $jam        Time string (HH:MM)
     * @param  string      $tanggal    Date string (Y-m-d)
     * @param  int|null    $excludeId  talkshowID to exclude (for updates)
     * @return true|string             true if available, error message if not
     */
    public function isJamTalkshowAvailable(string $jam, string $tanggal, ?int $excludeId = null): true|string
    {
        $query = narasumbertalkshow::whereNotNull('talkshow_schedule')
            ->whereDate('waktu_wawancara', $tanggal)
            ->whereRaw("TIME_FORMAT(waktu_wawancara, '%H:%i') = ?", [$jam]);

        if ($excludeId !== null) {
            $query->where('talkshowID', '!=', $excludeId);
        }

        $conflict = $query->first();

        if ($conflict) {
            return "Jam {$jam} pada tanggal tersebut sudah digunakan oleh narasumber talkshow lain ({$conflict->nama}).";
        }

        return true;
    }

    /**
     * Check whether the given JAM (HH:MM) on a specific TANGGAL
     * is not already used by another scheduled byphone narasumber.
     *
     * Same time on a different day is ALLOWED.
     * Same time on the SAME day is BLOCKED.
     *
     * @param  string      $jam        Time string (HH:MM)
     * @param  string      $tanggal    Date string (Y-m-d)
     * @param  int|null    $excludeId  byphoneID to exclude (for updates)
     * @return true|string             true if available, error message if not
     */
    public function isJamByphoneAvailable(string $jam, string $tanggal, ?int $excludeId = null): true|string
    {
        $query = narasumberbyphone::whereNotNull('byphone_schedule')
            ->whereDate('waktu_wawancara', $tanggal)
            ->whereRaw("TIME_FORMAT(waktu_wawancara, '%H:%i') = ?", [$jam]);

        if ($excludeId !== null) {
            $query->where('byphoneID', '!=', $excludeId);
        }

        $conflict = $query->first();

        if ($conflict) {
            return "Jam {$jam} pada tanggal tersebut sudah digunakan oleh narasumber byphone lain ({$conflict->nama}).";
        }

        return true;
    }

    /**
     * Extract HH:MM from a datetime string.
     *
     * @param  string  $datetime  e.g. "2026-04-19 19:00:00"
     * @return string             e.g. "19:00"
     */
    public function extractJam(string $datetime): string
    {
        return \Carbon\Carbon::parse($datetime)->format('H:i');
    }

    /**
     * Extract Y-m-d from a datetime string.
     *
     * @param  string  $datetime  e.g. "2026-04-19 19:00:00"
     * @return string             e.g. "2026-04-19"
     */
    public function extractTanggal(string $datetime): string
    {
        return \Carbon\Carbon::parse($datetime)->format('Y-m-d');
    }
}

