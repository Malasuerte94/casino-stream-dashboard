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
        Schema::create('bonus_buy_games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bonus_buy_id');
            $table->string('name')->nullable();
            $table->string('stake')->nullable();
            $table->string('price')->nullable();
            $table->string('result')->nullable();
            $table->string('multiplier')->nullable();
            $table->timestamps();
            $table->index('bonus_buy_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonus_buy_games');
    }
};
