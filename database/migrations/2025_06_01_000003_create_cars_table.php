<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rating tidak disimpan di sini: dihitung dari ulasan pada tabel bookings.
        // Foto disimpan di tabel car_photos.
        Schema::create('cars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('brand_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('model_code', 20)->nullable();
            $table->foreignUuid('category_id')->constrained()->restrictOnDelete();
            $table->enum('transmission', ['Matic', 'Manual']);
            $table->enum('fuel', ['Bensin', 'Diesel', 'Hybrid', 'Listrik']);
            $table->string('fuel_grade', 50)->nullable();
            $table->unsignedTinyInteger('seats');
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedSmallInteger('power_hp')->nullable();
            $table->decimal('acceleration', 3, 1)->nullable()->comment('0-100 km/jam, detik');
            $table->unsignedInteger('price_per_day');
            $table->text('description');
            $table->string('condition_note')->default('Kendaraan dalam keadaan baik');
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });

        Schema::create('car_photos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('car_id')->constrained()->cascadeOnDelete();
            $table->string('path');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('car_photos');
        Schema::dropIfExists('cars');
    }
};
