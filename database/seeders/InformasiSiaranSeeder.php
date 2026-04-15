<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InformasiSiaranSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $siarans = [
            [
                'tanggal'    => '2026-04-15',
                'waktu'      => '06:00:00',
                'keterangan' => 'Siaran pagi - berita nasional dan lokal',
            ],
            [
                'tanggal'    => '2026-04-15',
                'waktu'      => '10:00:00',
                'keterangan' => 'Talkshow kesehatan bersama narasumber dari RSUD',
            ],
            [
                'tanggal'    => '2026-04-15',
                'waktu'      => '13:00:00',
                'keterangan' => 'Siaran siang - musik dan hiburan',
            ],
            [
                'tanggal'    => '2026-04-16',
                'waktu'      => '07:00:00',
                'keterangan' => 'Program olahraga pagi',
            ],
            [
                'tanggal'    => '2026-04-16',
                'waktu'      => '19:00:00',
                'keterangan' => 'Siaran malam - berita dan diskusi publik',
            ],
        ];

        foreach ($siarans as $data) {
            DB::table('informasi_siarans')->insert(array_merge($data, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
