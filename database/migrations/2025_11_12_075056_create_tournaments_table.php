<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('max_players')->default(30);
            $table->integer('current_players')->default(0);
            $table->enum('status', ['upcoming', 'active', 'completed', 'cancelled'])->default('upcoming');
            $table->dateTime('start_date');
            $table->dateTime('end_date')->nullable();
            $table->foreignId('winner_id')->nullable()->constrained('players');
            $table->integer('entry_fee')->default(0);
            $table->integer('prize_pool')->default(0);
            $table->json('rules')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tournaments');
    }
};