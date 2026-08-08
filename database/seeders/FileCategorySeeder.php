<?php

namespace Database\Seeders;

use App\Models\FileCategory;
use Illuminate\Database\Seeder;

class FileCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Modul Teknis',      'description' => 'Modul teknis operasional dan prosedur standar.'],
            ['name' => 'Panduan Pengguna',   'description' => 'Panduan dan tutorial penggunaan sistem.'],
            ['name' => 'Arsip Administrasi', 'description' => 'Arsip dokumen administrasi dan kelengkapan resmi.'],
            ['name' => 'Template Dokumen',   'description' => 'Template surat, formulir, dan dokumen standar.'],
        ];

        foreach ($categories as $data) {
            FileCategory::firstOrCreate(
                ['name' => $data['name']],
                [
                    'description' => $data['description'],
                    'created_by'  => 1,
                    'updated_by'  => 1,
                ]
            );
        }
    }
}
