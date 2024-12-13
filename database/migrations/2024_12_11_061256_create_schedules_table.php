<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id(); // ID unik untuk setiap jadwal
            $table->foreignId('restaurant_id')->constrained('restaurants')->onDelete('cascade'); // Foreign key ke restoran
            $table->enum('day_of_week', [
                'Mon',
                'Tue',
                'Wed',
                'Thu',
                'Fri',
                'Sat',
                'Sun',
            ])->nullable(false); // Hari dalam seminggu (Mon, Tue, ...)
            $table->time('open_time')->nullable(false); // Waktu buka
            $table->time('close_time')->nullable(false); // Waktu tutup
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
