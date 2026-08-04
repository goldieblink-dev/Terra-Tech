<?php

namespace Database\Seeders;

use App\Models\CompanyProfile;
use Illuminate\Database\Seeder;

class CompanyProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanyProfile::updateOrCreate(
            ['id' => 1],
            [
                'company_name' => 'Terra Tech Indonesia',
                'tagline' => 'Leading Agricultural & Geological Technology Solutions',
                'description' => 'Terra Tech Indonesia adalah penyedia solusi teknologi terdepan dalam bidang agrikultur, pemetaan geospasial, dan kecerdasan terapan untuk keberlanjutan industri.',
                'email' => 'info@terratech.test',
                'phone' => '+62 21 555 1234',
                'address' => 'Jl. Jendral Sudirman No. 88, Jakarta Selatan, 12190, Indonesia',
                'facebook_url' => 'https://facebook.com/terratech',
                'instagram_url' => 'https://instagram.com/terratech',
                'linkedin_url' => 'https://linkedin.com/company/terratech',
                'youtube_url' => 'https://youtube.com/@terratech',
            ]
        );

        CompanyProfile::clearCache();
    }
}
