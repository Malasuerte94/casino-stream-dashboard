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
        Schema::table('winners', function (Blueprint $table) {
            $table->dropColumn(['win_result', 'win_big_m', 'win_small_m', 'win_game', 'user_id']);
            $table->unsignedBigInteger('win_closest_result')->nullable()->after('bonus_buy_id');
            $table->unsignedBigInteger('win_closest_biggest_multi')->nullable()->after('bonus_buy_id');
            $table->unsignedBigInteger('win_closest_lowest_multi')->nullable()->after('bonus_buy_id');
            $table->unsignedBigInteger('win_exact_biggest_multi_game')->nullable()->after('bonus_buy_id');
            $table->unsignedBigInteger('win_exact_lowest_multi_game')->nullable()->after('bonus_buy_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('winners', function (Blueprint $table) {
            $table->dropColumn([
                'win_closest_result',
                'win_closest_biggest_multi',
                'win_closest_lowest_multi',
                'win_exact_biggest_multi_game',
                'win_exact_lowest_multi_game'
            ]);
            $table->boolean('win_result')->default(false)->after('bonus_buy_id');
            $table->boolean('win_big_m')->default(false)->after('win_result');
            $table->boolean('win_small_m')->default(false)->after('win_big_m');
            $table->boolean('win_game')->default(false)->after('win_small_m');
            $table->unsignedBigInteger('user_id')->after('id');
        });
    }
};
