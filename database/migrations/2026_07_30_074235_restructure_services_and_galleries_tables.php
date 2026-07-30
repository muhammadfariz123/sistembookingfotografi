<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah tabel paket (service_types)
        Schema::table('service_types', function (Blueprint $table) {
            $table->dropColumn('category_name');
            // Tambahkan relasi ke kategori (Dibuat nullable agar aman)
            $table->foreignId('service_category_id')->nullable()->after('user_id')->constrained('service_categories')->nullOnDelete();
        });

        // 2. Ubah tabel galeri (service_galleries)
        Schema::table('service_galleries', function (Blueprint $table) {
            $table->dropForeign(['service_type_id']);
            $table->dropColumn('service_type_id');
            // Tambahkan relasi ke kategori (Dibuat nullable agar aman dari bentrok data lama)
            $table->foreignId('service_category_id')->nullable()->after('id')->constrained('service_categories')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // Kosongkan saja untuk mempermudah
    }
};