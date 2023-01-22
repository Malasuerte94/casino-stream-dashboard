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
        Schema::create('bonus_hunt_games', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bonus_hunt_id');
            $table->string('name')->nullable();
            $table->string('stake')->nullable();
            $table->string('result')->nullable();
            $table->string('multiplier')->nullable();
            $table->timestamps();
            $table->index('bonus_hunt_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bonus_hunt_games');
    }
};
