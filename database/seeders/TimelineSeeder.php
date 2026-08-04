<?php

namespace Database\Seeders;

use App\Models\Timeline;
use Illuminate\Database\Seeder;

class TimelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Timeline::updateOrCreate(
            ['title' => 'Pendirian Terra Tech Indonesia'],
            [
                'description' => 'Resmi didirikan sebagai entitas riset dan pengembang teknologi geospasial presisi terdepan di Indonesia.',
                'start_date'  => '2024-01-15',
                'end_date'    => '2024-01-15',
                'location'    => 'Jakarta, Indonesia',
                'color'       => '#3b82f6',
                'icon'        => 'flag',
                'status'      => 'published',
                'sort_order'  => 1,
            ]
        );

        Timeline::updateOrCreate(
            ['title' => 'Peluncuran Sensor Geospasial V1'],
            [
                'description' => 'Uji coba operasional dan komersialisasi pertama perangkat sensor spasial berbasis kecerdasan buatan.',
                'start_date'  => '2025-06-01',
                'end_date'    => '2025-12-31',
                'location'    => 'Bandung & Yogyakarta',
                'color'       => '#10b981',
                'icon'        => 'cpu',
                'status'      => 'published',
                'sort_order'  => 2,
            ]
        );

        Timeline::updateOrCreate(
            ['title' => 'Ekspansi Platform AI Terra Tech 2026'],
            [
                'description' => 'Pengembangan arsitektur kecerdasan presisi skala nasional untuk pengolahan pemetaan wilayah real-time.',
                'start_date'  => '2026-03-01',
                'end_date'    => '2026-11-30',
                'location'    => 'Seluruh Indonesia',
                'color'       => '#6366f1',
                'icon'        => 'globe',
                'status'      => 'published',
                'sort_order'  => 3,
            ]
        );

        Timeline::clearCache();
    }
}
