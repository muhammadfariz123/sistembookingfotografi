<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->text('link_folder_kerja')->nullable()->after('link_hasil');
            $table->text('link_original')->nullable()->after('link_folder_kerja');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['link_folder_kerja', 'link_original']);
        });
    }
};