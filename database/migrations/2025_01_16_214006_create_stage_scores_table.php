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
        Schema::create('stage_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bracket_id')->index();
            $table->foreignId('bonus_concurrent_id')->index();
            $table->float('cost_buy')->default(0);
            $table->float('result_buy')->default(0);
            $table->float('score', 10, 3)->default(0);
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
        Schema::dropIfExists('stage_scores');
    }
};
