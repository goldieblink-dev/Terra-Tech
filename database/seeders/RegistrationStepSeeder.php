<?php

namespace Database\Seeders;

use App\Models\RegistrationStep;
use Illuminate\Database\Seeder;

class RegistrationStepSeeder extends Seeder
{
    public function run(): void
    {
        $steps = [
            [
                'title' => 'Langkah 1: Registrasi Akun & Pengisian Data Diri',
                'description' => 'Calon pendaftar wajib membuat akun baru di portal resmi Terra Tech dan melengkapi biodata diri secara benar dan valid.',
                'requirements' => [
                    'Alamat email aktif & nomor WhatsApp valid',
                    'Scan KTP / Kartu Identitas Resmi',
                    'Pas foto berwarna terbaru ukuran 3x4',
                ],
                'icon' => 'user-plus',
                'sort_order' => 1,
                'status' => 'published',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'title' => 'Langkah 2: Unggah Dokumen Syarat Teknis',
                'description' => 'Setelah akun terverifikasi, unggah kelengkapan berkas teknis dan kualifikasi yang dibutuhkan oleh tim validator.',
                'requirements' => [
                    'Ijazah / Sertifikat Keahlian Terakhir (PDF)',
                    'Surat Pernyataan Kesediaan Operasional (Signed PDF)',
                    'Portofolio / Rekam Jejak Proyek Terakhir',
                ],
                'icon' => 'file-text',
                'sort_order' => 2,
                'status' => 'published',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'title' => 'Langkah 3: Pembayaran & Konfirmasi Administrasi',
                'description' => 'Lakukan konfirmasi administrasi dan pembayaran biaya pendaftaran melalui metode pembayaran resmi yang tersedia.',
                'requirements' => [
                    'Bukti Transfer / Konfirmasi Pembayaran Resmi',
                    'Kode Referensi Registrasi Akun',
                ],
                'icon' => 'credit-card',
                'sort_order' => 3,
                'status' => 'published',
                'created_by' => 1,
                'updated_by' => 1,
            ],
            [
                'title' => 'Langkah 4: Penerbitan Akses & Sertifikat Resmi',
                'description' => 'Setelah seluruh berkas dan konfirmasi disetujui, pendaftar akan menerima Surat Keputusan dan akses penuh ke ekosistem Terra Tech.',
                'requirements' => [
                    'Akun Akses Ekosistem Terverifikasi',
                    'Dokumen Penetapan Resmi (E-Certificate / SK)',
                ],
                'icon' => 'award',
                'sort_order' => 4,
                'status' => 'published',
                'created_by' => 1,
                'updated_by' => 1,
            ],
        ];

        foreach ($steps as $data) {
            RegistrationStep::firstOrCreate(
                ['title' => $data['title']],
                $data
            );
        }
    }
}
