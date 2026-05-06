<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('game_moves', function (Blueprint $table) {
            $table->string('selected_option')->nullable()->after('correct_answer');
        });
    }

    public function down()
    {
        Schema::table('game_moves', function (Blueprint $table) {
            $table->dropColumn('selected_option');
        });
    }
};