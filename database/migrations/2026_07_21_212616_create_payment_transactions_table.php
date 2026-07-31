<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // pemilik venue/fotografer
            $table->string('transaction_id')->unique(); // Contoh: BKG-20260721-003-DP-1784547840
            $table->string('payment_type'); // DP, PELUNASAN, LUNAS
            $table->bigInteger('amount'); // Nominal yang dibayar pada transaksi ini
            $table->string('payment_status')->default('Tunggu Konfirmasi'); // Tunggu Konfirmasi, Berhasil, Ditolak, Expired
            $table->string('payment_proof')->nullable(); // Foto bukti transfer
            $table->text('admin_notes')->nullable(); // Catatan admin / Add notes
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};