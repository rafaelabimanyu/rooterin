<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimonial;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Testimonial::truncate();

        $testimonials = [
            [
                'name' => 'Bpk. Ahmad Subarjo', 
                'photo' => 'https://i.pravatar.cc/150?u=ahmad', 
                'rating' => 5, 
                'content' => 'Sangat puas dengan layanan pembersihan pipa menggunakan CCTV Drain Camera. Wastafel dapur mampet bertahun-tahun langsung plong tanpa merusak ubin dapur sama sekali!'
            ],
            [
                'name' => 'Ibu Maria Natalia', 
                'photo' => 'https://i.pravatar.cc/150?u=maria', 
                'rating' => 5, 
                'content' => 'Teknisi Rooterin sangat sopan, profesional, dan bekerja cepat. Pipa pembuangan kamar mandi atas lancar kembali berkat alat Mechanical Rooter modern.'
            ],
            [
                'name' => 'Bpk. H. Hendra Kurniawan (B2B Commercial)', 
                'photo' => 'https://i.pravatar.cc/150?u=hendra', 
                'rating' => 5, 
                'content' => 'Layanan grease trap dan pipa restoran kami dibersihkan secara terjadwal oleh tim Rooterin. Pengerjaannya rapi, higienis, dan tidak mengganggu jam operasional bisnis kami.'
            ],
            [
                'name' => 'Ibu Rina Amalia', 
                'photo' => 'https://i.pravatar.cc/150?u=rina', 
                'rating' => 5, 
                'content' => 'Sistem saluran air bersih kami yang tersumbat lumut tebal langsung bersih total setelah proses pembersihan toren & pipa. Recommended!'
            ],
            [
                'name' => 'Bpk. Dani Setiawan', 
                'photo' => 'https://i.pravatar.cc/150?u=dani', 
                'rating' => 4, 
                'content' => 'Respons admin cepat dan teknisi datang tepat waktu menggunakan seragam lengkap bersertifikat. Saluran air kotor kembali normal dalam 45 menit.'
            ]
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}
