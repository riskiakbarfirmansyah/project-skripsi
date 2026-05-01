<?php

namespace Database\Seeders;

use App\Models\informasisiaran;
use Illuminate\Database\Seeder;

class DummySiaranSeeder extends Seeder
{
    public function run(): void
    {
        $keteranganList = [
            'Siaran Pagi - Program Inspirasi',
            'Talkshow Kesehatan Masyarakat',
            'Berita Siang - Update Kota',
            'Diskusi Pendidikan Anak Muda',
            'Program Musik Nusantara Sore',
            'Dialog Lingkungan Hidup',
            'Siaran Sore - Info Lalu Lintas',
            'Wawancara Eksklusif Tokoh Daerah',
            'Acara Budaya Lokal Mingguan',
            'Siaran Malam - Relaksasi Musik',
            'Program UMKM Kota Kita',
            'Kajian Islam Subuh Berkah',
            'Obrolan Santai Komunitas',
            'Info Cuaca dan Bencana Alam',
            'Siaran Olahraga - Liga Daerah',
            'Program Kesehatan Mental Remaja',
            'Diskusi Ekonomi Kreatif',
            'Laporan Khusus Pembangunan',
            'Program Sastra dan Puisi',
            'Siaran Penutup - Refleksi Hari Ini',
        ];

        for ($i = 0; $i < 20; $i++) {
            informasisiaran::create([
                'tanggal'    => now()->subDays(rand(0, 30))->format('Y-m-d'),
                'waktu'      => str_pad(rand(6, 22), 2, '0', STR_PAD_LEFT) . ':' . (rand(0, 1) ? '00' : '30') . ':00',
                'keterangan' => $keteranganList[$i],
                'status'     => 'pending',
            ]);
        }
    }
}
