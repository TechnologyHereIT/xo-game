<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('game_moves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade');
            $table->foreignId('player_id')->constrained('players');
            $table->integer('position');
            $table->boolean('correct_answer')->default(false);
            $table->unsignedBigInteger('question_id')->nullable();
            $table->boolean('is_speed_round')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_moves');
    }
};