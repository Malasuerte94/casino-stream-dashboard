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
        Schema::create('battle_predictions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bonus_battle_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('game_winner_id');
            $table->unsignedBigInteger('game_biggest_multi_id');
            $table->unsignedBigInteger('game_lowest_multi_id');
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
        Schema::dropIfExists('battle_predictions');
    }
};
