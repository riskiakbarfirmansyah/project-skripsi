<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NarasumberTalkshowSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $narasumbers = [
            [
                'nama'             => 'Prof. Dr. Hendra Kusuma',
                'instansi'         => 'Universitas Negeri Setempat',
                'nomor_telp'       => '081398765432',
                'tema_wawancara'   => 'Pendidikan Karakter di Era Digital',
                'waktu_wawancara'  => '2026-04-15 13:00:00',
                'draft_pertanyaan' => "1. Bagaimana dampak media sosial terhadap karakter anak?\n2. Apa peran orang tua dalam mendampingi anak di era digital?\n3. Program apa yang diusung kampus untuk meningkatkan literasi digital?",
            ],
            [
                'nama'             => 'H. Faisal Rahman',
                'instansi'         => 'Kamar Dagang dan Industri (KADIN)',
                'nomor_telp'       => '085712345678',
                'tema_wawancara'   => 'Peluang Investasi dan UMKM Lokal',
                'waktu_wawancara'  => '2026-04-16 14:00:00',
                'draft_pertanyaan' => "1. Sektor UMKM mana yang paling potensial saat ini?\n2. Bagaimana akses permodalan bagi pelaku UMKM baru?\n3. Program pelatihan apa yang tersedia?",
            ],
            [
                'nama'             => 'Dra. Wulandari, M.Si.',
                'instansi'         => 'Badan Lingkungan Hidup',
                'nomor_telp'       => '081234509876',
                'tema_wawancara'   => 'Pengelolaan Sampah dan Lingkungan Hidup',
                'waktu_wawancara'  => '2026-04-18 10:00:00',
                'draft_pertanyaan' => "1. Berapa ton sampah yang dihasilkan kota per hari?\n2. Apa solusi pengelolaan sampah organik?\n3. Bagaimana masyarakat bisa berpartisipasi?",
            ],
        ];

        foreach ($narasumbers as $data) {
            DB::table('narasumber_talkshows')->insert(array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
