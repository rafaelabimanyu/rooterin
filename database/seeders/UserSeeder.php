<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin RooterIn',
            'email' => 'admin@rooterin.com',
            'role' => 'super_admin',
            'password' => bcrypt('password'),
        ]);
    }
}
