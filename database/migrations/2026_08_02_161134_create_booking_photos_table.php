<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->cascadeOnDelete();
            $table->string('filename');
            $table->string('file_url'); // Path atau URL lokasi gambar tersimpan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_photos');
    }
};