<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('service_type_id')
                  ->constrained()
                  ->onDelete('restrict');

            // Data klien
            $table->string('client_name');
            $table->string('client_contact')->nullable();
            $table->string('client_address')->nullable();

            // Tanggal & waktu
            $table->date('booking_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('booking_time')->nullable();

            // Status
            $table->string('status')->default('Dijadwalkan');
            // payment_status dihitung otomatis TPS, disimpan hasil kalkulasi
            $table->string('payment_status')->default('Belum Bayar');

            // TPS — input
            $table->integer('quantity')->default(1);
            $table->bigInteger('unit_price')->default(0);
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->bigInteger('paid_amount')->default(0);

            // TPS — hasil kalkulasi otomatis (disimpan agar bisa query/filter)
            $table->bigInteger('subtotal')->default(0);
            $table->bigInteger('discount_amount')->default(0);
            $table->bigInteger('total')->default(0);
            $table->bigInteger('remaining')->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};