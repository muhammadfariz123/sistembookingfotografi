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

        // akun kelima
        User::updateOrCreate(
            ['email' => '2211104069@ittelkom-pwt.ac.id'],
            ['password' => Hash::make('password123')]
        );


        // akun kelima
        User::updateOrCreate(
            ['email' => 'mfarizzzz778@gmail.com'],
            ['password' => Hash::make('password123')]
        );

        // Akun keenam
        User::updateOrCreate(
            ['email' => 'hidayatfariz14@gmail.com'],
            ['password' => Hash::make('password')]
        );

        // Akun ketujuh
        User::updateOrCreate(
            ['email' => 'jihannn2211@gmail.com'],
            ['password' => Hash::make('password')]
        );

        // Akun Pertama (warungkombas@gmail.com)
        User::updateOrCreate(
            ['email' => 'Adobe4290@gmail.com'],
            ['password' => Hash::make('password123')]
        );


        // Akun Pertama (warungkombas@gmail.com)
        User::updateOrCreate(
            ['email' => 'nrtibul@gmail.com'],
            ['password' => Hash::make('password123')]
        );

        // Akun Kedua (muhammadfarizznur12@gmail.com)
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            ['password' => Hash::make('password')]
        );


        User::updateOrCreate(
            ['email' => 'Rakhmat.wijaya13@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'tigapersonel@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'photogatta.id@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'setiawanferdi548@gmail.com'],
            ['password' => Hash::make('password')]
        );

        User::updateOrCreate(
            ['email' => 'Bimahousestudio@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'prast.dn02@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'aderanuha08@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'Kelviinsteffanes@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'Admin@talebearing.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'menorehkisah2@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'restutripamungkas@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'tproductionmas@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'memoraphoto02@gmail.com'],
            ['password' => Hash::make('password')]
        );

        User::updateOrCreate(
            ['email' => 'skuyfoto@gd-3.uno'],
            ['password' => Hash::make('password')]
        );

        User::updateOrCreate(
            ['email' => 'Dhopex.89@gmail.com'],
            ['password' => Hash::make('password')]
        );

        User::updateOrCreate(
            ['email' => 'jproductionid1@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'pixelgraphy01@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'Kawanmahasiswa5@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'mulangsimkomdig@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'pkrsdanu2@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'setiakuovie@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'adi10bowo@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'zidan6179@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'putraferdiansah546@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'terserahaja470@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'Projectalurcerita@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'rijalgunawan208@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'irfanmlna273@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'Arif.290166@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'nurussalma00@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'unni041100@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'dianthuram21@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'Anandabian19@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'jamgablok01@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'Nhsstudio2021@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'dryestudio2026@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'ricodwiagustin1234@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'Akmalzaky.a@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'aaperturesharing@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'adi.photography10@gmail.com'],
            ['password' => Hash::make('password')]
        );
        User::updateOrCreate(
            ['email' => 'bermuaramoment@gmail.com'],
            ['password' => Hash::make('password')]
        );

    }
}