<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NarasumberByphoneSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $narasumbers = [
            [
                'nama'             => 'Dr. Budi Santoso',
                'instansi'         => 'RSUD Kota',
                'nomor_telp'       => '081234567890',
                'tema_wawancara'   => 'Pencegahan Penyakit Demam Berdarah',
                'waktu_wawancara'  => '2026-04-15 10:00:00',
                'draft_pertanyaan' => "1. Apa saja gejala awal demam berdarah?\n2. Bagaimana cara pencegahan yang efektif?\n3. Kapan penderita harus segera dibawa ke rumah sakit?",
            ],
            [
                'nama'             => 'Ir. Siti Rahayu',
                'instansi'         => 'Dinas Pertanian',
                'nomor_telp'       => '082198765432',
                'tema_wawancara'   => 'Program Ketahanan Pangan Daerah',
                'waktu_wawancara'  => '2026-04-16 09:00:00',
                'draft_pertanyaan' => "1. Apa target produksi pangan tahun ini?\n2. Bagaimana program subsidi pupuk berjalan?\n3. Kendala apa yang dihadapi petani lokal?",
            ],
            [
                'nama'             => 'Kombes Agus Widodo',
                'instansi'         => 'Polres Kota',
                'nomor_telp'       => '081312345678',
                'tema_wawancara'   => 'Keamanan Lalu Lintas Menjelang Lebaran',
                'waktu_wawancara'  => '2026-04-17 11:00:00',
                'draft_pertanyaan' => "1. Berapa personel yang disiagakan?\n2. Titik rawan macet di mana saja?\n3. Imbauan untuk pemudik?",
            ],
        ];

        foreach ($narasumbers as $data) {
            DB::table('narasumber_byphones')->insert(array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
