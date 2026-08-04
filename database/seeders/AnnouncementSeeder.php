<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Announcement::updateOrCreate(
            ['slug' => 'pengumuman-jadwal-pemeliharaan-sistem-server-terra-tech'],
            [
                'title'           => 'Pengumuman Jadwal Pemeliharaan Sistem Server Terra Tech',
                'content'         => 'Diberitahukan kepada seluruh anggota tim dan mitra bahwa akan dilaksanakan pemeliharaan rutin server Terra Tech pada hari Sabtu mendatang. Layanan API dan CMS mungkin mengalami kendala singkat selama jeda waktu tersebut.',
                'priority'        => 'urgent',
                'status'          => 'published',
                'published_at'    => now()->subDays(1),
                'downloads_count' => 5,
            ]
        );

        Announcement::updateOrCreate(
            ['slug' => 'penerapan-kebijakan-keamanan-data-geospasial-terbaru-2026'],
            [
                'title'           => 'Penerapan Kebijakan Keamanan Data Geospasial Terbaru 2026',
                'content'         => 'Sehubungan dengan pembaharuan standar privasi data industri, Terra Tech memberlakukan protokol enkripsi baru untuk penyimpanan koordinat spasial dan dokumen operasional.',
                'priority'        => 'important',
                'status'          => 'published',
                'published_at'    => now()->subDays(3),
                'downloads_count' => 12,
            ]
        );

        Announcement::updateOrCreate(
            ['slug' => 'pendaftaran-program-pelatihan-teknologi-presisi-internal'],
            [
                'title'           => 'Pendaftaran Program Pelatihan Teknologi Presisi Internal',
                'content'         => 'Kesempatan bagi seluruh staf operator dan teknisi untuk mengikuti sertifikasi internal pengoperasian instrumen geospasial generasi ke-4.',
                'priority'        => 'normal',
                'status'          => 'published',
                'published_at'    => now()->subDays(5),
                'downloads_count' => 2,
            ]
        );

        Announcement::clearCache();
    }
}
