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
        Schema::table('users', function (Blueprint $table) {
            $table->text('streamlabs_access_token')->nullable()->after('yt_code');
            $table->text('streamlabs_refresh_token')->nullable()->after('yt_code');
            $table->text('streamlabs_expires_in')->nullable()->after('yt_code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['streamlabs_access_token', 'streamlabs_refresh_token', 'streamlabs_expires_in']);
        });
    }
};
