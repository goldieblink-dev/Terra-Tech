<?php

namespace Database\Seeders;

use App\Models\InformationCategory;
use App\Models\InformationPost;
use Illuminate\Database\Seeder;

class InformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cat1 = InformationCategory::updateOrCreate(
            ['slug' => 'berita-perusahaan'],
            ['name' => 'Berita Perusahaan', 'description' => 'Informasi dan rilis pers seputar perkembangan perusahaan Terra Tech.']
        );

        $cat2 = InformationCategory::updateOrCreate(
            ['slug' => 'teknologi-agrikultur'],
            ['name' => 'Teknologi Agrikultur', 'description' => 'Artikel teknis dan wawasan terapan presisi teknologi sektor pertanian.']
        );

        $cat3 = InformationCategory::updateOrCreate(
            ['slug' => 'inovasi-geospatial'],
            ['name' => 'Inovasi Geospatial', 'description' => 'Publikasi riset dan solusi pemetaan geospasial kecerdasan buatan.']
        );

        InformationPost::updateOrCreate(
            ['slug' => 'terra-tech-resmikan-inovasi-sensor-geospasial-generasi-terbaru'],
            [
                'category_id' => $cat3->id,
                'title' => 'Terra Tech Resmikan Inovasi Sensor Geospasial Generasi Terbaru',
                'excerpt' => 'Terra Tech Indonesia meluncurkan sensor geospasial generasi terbaru untuk mendukung otomatisasi pemetaan lahan presisi tinggi.',
                'content' => '<p>Terra Tech Indonesia secara resmi mengumumkan peluncuran solusi sensor geospasial generasi terbaru yang dirancang untuk mendukung industri agrikultur dan pemetaan wilayah berskala besar.</p><p>Dengan teknologi AI terintegrasi, sensor ini mampu mendeteksi kualifikasi tanah dan tingkat kelembapan secara real-time dengan akurasi hingga 99,8%.</p>',
                'featured_image_alt' => 'Sensor Geospasial Terra Tech',
                'meta_title' => 'Inovasi Sensor Geospasial Terbaru - Terra Tech Indonesia',
                'meta_description' => 'Solusi pemetaan geospasial generasi terbaru dari Terra Tech Indonesia dengan integrasi kecerdasan terapan presisi tinggi.',
                'status' => 'published',
                'published_at' => now(),
                'views_count' => 125,
            ]
        );

        InformationPost::clearCache();
    }
}
