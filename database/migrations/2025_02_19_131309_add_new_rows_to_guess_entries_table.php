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
        Schema::table('guess_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('game_highest_id')->nullable()->after('bonus_buy_id');
            $table->unsignedBigInteger('game_lowest_id')->nullable()->after('bonus_buy_id');
            $table->dropColumn('game_winner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('guess_entries', function (Blueprint $table) {
            $table->string('game_winner')->nullable();
            $table->dropColumn(['game_highest_id', 'game_lowest_id']);
        });
    }
};
