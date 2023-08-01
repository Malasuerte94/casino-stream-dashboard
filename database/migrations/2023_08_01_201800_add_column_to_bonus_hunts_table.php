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
            $table->foreignId('stream_id')->index()->after('user_id');
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
            $table->dropColumn('stream_id');
            $table->dropForeign(['stream_id']);
            $table->dropIndex(['stream_id']);
        });
    }
};
