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
        Schema::table('bonus_buys', function (Blueprint $table) {
            $table->boolean('is_open')->default(false)->after('seed');
            $table->boolean('ended')->default(false)->after('seed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('bonus_buys', function (Blueprint $table) {
            $table->dropColumn('is_open');
            $table->dropColumn('ended');
        });
    }
};
