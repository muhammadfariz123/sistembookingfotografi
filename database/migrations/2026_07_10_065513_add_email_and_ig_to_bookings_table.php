<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('client_email')->nullable()->after('client_contact');
            $table->string('client_instagram')->nullable()->after('client_email');
        });
    }
    public function down(): void {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['client_email', 'client_instagram']);
        });
    }
};