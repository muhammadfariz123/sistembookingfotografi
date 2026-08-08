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
    // php artisan db:seed --class=AdminSeeder

    public function run(): void
    {
        // Akun Pertama (warungkombas@gmail.com)
        User::updateOrCreate(
            ['email' => 'warungkombas@gmail.com'],
            ['password' => Hash::make('password123')]
        );

        // Akun Kedua (muhammadfarizznur12@gmail.com)
        User::updateOrCreate(
            ['email' => 'muhammadfarizznur12@gmail.com'],
            ['password' => Hash::make('password')]
        );

        // Akun ketiga
        User::updateOrCreate(
            ['email' => 'michele77ji@gmail.com'],
            ['password' => Hash::make('password')]
        );

        // Akun keempat
        User::updateOrCreate(
            ['email' => 'gelaskacaa257@gmail.com'],
            ['password' => Hash::make('password')]
        );
    }
}