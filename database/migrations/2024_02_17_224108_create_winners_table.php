<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('winners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('bonus_hunt_id')->nullable();
            $table->unsignedBigInteger('bonus_buy_id')->nullable();

            $table->boolean('win_result')->default(0);
            $table->boolean('win_big_m')->default(0);
            $table->boolean('win_small_m')->default(0);
            $table->boolean('win_game')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('winners');
    }
};
