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
        Schema::create('guess_entries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('bonus_hunt_id')->nullable();
            $table->unsignedBigInteger('bonus_buy_id')->nullable();

            $table->string('estimated')->nullable();
            $table->string('game_winner')->nullable();
            $table->string('biggest_multi')->nullable();
            $table->string('lowest_multi')->nullable();

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
        Schema::dropIfExists('guess_entries');
    }
};
