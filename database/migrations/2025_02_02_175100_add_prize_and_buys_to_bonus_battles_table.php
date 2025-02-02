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
        Schema::table('bonus_battles', function (Blueprint $table) {
            $table->string('prize')->nullable()->after('stake');
            $table->integer('buys')->default(1)->after('stake');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bonus_battles', function (Blueprint $table) {
            $table->dropColumn(['prize', 'buys']);
        });
    }
};
