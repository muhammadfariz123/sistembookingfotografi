<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Tambahkan kolom link_hasil (dan pastikan kolom lain jika belum ada)
            if (!Schema::hasColumn('bookings', 'link_hasil')) {
                $table->string('link_hasil')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            if (Schema::hasColumn('bookings', 'link_hasil')) {
                $table->dropColumn('link_hasil');
            }
        });
    }
};