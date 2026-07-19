<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            // Mencari user berdasarkan email ini
            ['email' => 'warungkombas@gmail.com'],
            
            // Jika ada (atau belum ada), update/buat passwordnya
            [
                'password' => Hash::make('password123'),
            ]
        );
    }
}