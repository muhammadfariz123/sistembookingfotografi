<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_types', function (Blueprint $table) {
            // nullable karena mungkin tidak semua paket ada batas pilih fotonya (unlimited)
            $table->integer('photo_limit')->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('service_types', function (Blueprint $table) {
            $table->dropColumn('photo_limit');
        });
    }
};