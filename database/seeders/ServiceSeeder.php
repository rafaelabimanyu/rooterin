<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Saluran Pembuangan Mampet',
                'slug' => 'saluran-pembuangan-mampet',
                'icon' => 'ri-water-flash-fill',
                'description_short' => 'Solusi tuntas WC & pipa mampet dengan mesin Spiral Baja.',
                'description_full' => 'Menghancurkan sumbatan kerak lemak tanpa merusak konstruksi menggunakan teknologi modern tanpa bongkar.',
                'price' => 400000,
                'items' => ['Sal. Kamar Mandi', 'Sal. Cuci Piring', 'Sal. Cuci Tangan', 'Sal. Talang Air Hujan', 'Sal. Urinoir', 'Sal. Kloset', 'Sal. Bak Kontrol', 'Lain-lain'],
                'pricing' => [
                    ['type' => 'Rumah Hunian', 'price' => 'Rp. 400.000,-', 'note' => 'Per-titik Masalah, Garansi 30 Hari'],
                    ['type' => 'Komersial (Resto, Kantor, dll)', 'price' => 'Rp. 600.000 - 1.000.000', 'note' => 'Per-titik Masalah, Garansi 30 Hari']
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Air Bersih & Cuci Toren',
                'slug' => 'air-bersih-cuci-toren',
                'icon' => 'ri-drop-fill',
                'description_short' => 'Normalisasi kran mampet & cuci tangki air.',
                'description_full' => 'Teknik sterilisasi pipa untuk menjamin aliran air bersih yang sehat & lancar serta bebas lumut.',
                'price' => 200000,
                'items' => ['Kran Mampet', 'Cuci Toren / Tangki Air'],
                'pricing' => [
                    ['type' => 'Survey Lokasi', 'price' => 'Gratis', 'note' => 'Biaya ditentukan setelah survey lokasi']
                ],
                'is_active' => true,
            ],
            [
                'name' => 'Instalasi Sanitary & Pipa',
                'slug' => 'instalasi-sanitary-pipa',
                'icon' => 'ri-tools-fill',
                'description_short' => 'Pemasangan kran, kloset, & jalur pipa baru.',
                'description_full' => 'Dikerjakan dengan standar profesional untuk hasil rapi, kuat, & permanen menggunakan teknik presisi tinggi.',
                'price' => 0,
                'items' => ['Instalasi Pipa Air Bersih', 'Instalasi Pipa Air Kotor', 'Instalasi Kloset Jongkok/Duduk', 'Instalasi Sanitary', 'Instalasi Kran Air', 'Lain-lain'],
                'pricing' => [
                    ['type' => 'Project Based', 'price' => 'Custom Quote', 'note' => 'Berdasarkan volume pengerjaan & material']
                ],
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
