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
    public function up()
    {
        Schema::create('battle_prediction_winners', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bonus_battle_id');
            $table->unsignedBigInteger('game_winner_bp_id')->nullable();
            $table->unsignedBigInteger('game_lowest_bp_id')->nullable();
            $table->unsignedBigInteger('game_highest_bp_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('battle_prediction_winners');
    }
};
