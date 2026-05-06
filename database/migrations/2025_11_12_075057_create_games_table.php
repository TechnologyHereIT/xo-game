<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('game_type'); // computer, online, tournament
            $table->foreignId('player1_id')->constrained('players');
            $table->foreignId('player2_id')->nullable()->constrained('players');
            $table->enum('status', ['waiting', 'active', 'completed', 'cancelled'])->default('waiting');
            $table->enum('current_turn', ['player1', 'player2'])->default('player1');
            $table->json('board')->nullable();
            $table->string('winner')->nullable(); // 'X', 'O', or null
            $table->foreignId('tournament_id')->nullable()->constrained();
            
            // ✅ حقول جولة السرعة
            $table->boolean('speed_round_activated')->default(false);
            $table->boolean('speed_round_used')->default(false);
            
            // ✅ حقول البطاقات
            $table->json('player1_powerups')->nullable();
            $table->json('player2_powerups')->nullable();
            
            $table->integer('player1_score')->default(0);
            $table->integer('player2_score')->default(0);
            $table->integer('round_number')->default(1);
            
            // ✅ حقل لتتبع وقت الانتهاء
            $table->timestamp('completed_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('games');
    }
};