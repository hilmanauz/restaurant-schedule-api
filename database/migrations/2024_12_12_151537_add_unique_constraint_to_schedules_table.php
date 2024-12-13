<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToSchedulesTable extends Migration
{
    public function up()
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->unique(['restaurant_id', 'day_of_week'], 'unique_schedule_per_day');
        });
    }

    public function down()
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Hapus foreign key (sesuaikan nama FK sesuai skema database Anda)
            $table->dropForeign(['restaurant_id']);

            // Hapus index unik
            $table->dropUnique('unique_schedule_per_day');

            // Tambahkan kembali foreign key (opsional, jika diperlukan dalam migration lain)
            $table->foreign('restaurant_id')->references('id')->on('restaurants')->onDelete('cascade');
        });
    }
}
