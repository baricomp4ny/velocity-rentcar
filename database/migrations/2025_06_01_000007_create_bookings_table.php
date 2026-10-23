<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Data penyewa diambil dari tabel users. Durasi dan total harga dihitung
        // dari tanggal, harga sewa yang disepakati, dan diskon.
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code', 20)->unique();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('car_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('promo_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('pickup_location_id')->constrained('locations')->restrictOnDelete();
            $table->date('pickup_date');
            $table->date('return_date');
            $table->unsignedInteger('price_per_day');
            $table->unsignedInteger('discount')->default(0);
            $table->foreignUuid('payment_method_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['paid', 'ongoing', 'completed', 'cancelled'])->default('paid');
            $table->unsignedTinyInteger('rating')->nullable();
            $table->text('review')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['car_id', 'status', 'pickup_date', 'return_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
