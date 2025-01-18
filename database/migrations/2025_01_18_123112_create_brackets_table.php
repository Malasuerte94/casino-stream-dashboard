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
        Schema::create('brackets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bonus_stage_id')->constrained()->onDelete('cascade');
            $table->foreignId('participant_a_id');
            $table->foreignId('participant_b_id')->nullable();
            $table->boolean('is_finished')->default(false);
            $table->foreignId('winner_id')->nullable();
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
        Schema::dropIfExists('brackets');
    }
};
