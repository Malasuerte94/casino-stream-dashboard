<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bonus_hunts', function (Blueprint $table) {
            $table->foreignId('user_id')->index()->after('id');
            $table->string('start')->nullable()->after('name');
            $table->string('result')->nullable()->after('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bonus_hunts', function (Blueprint $table) {
            $table->dropColumn('start');
            $table->dropColumn('result');
            $table->dropForeign('user_id');
            $table->dropIndex('user_id');
        });
    }
};
