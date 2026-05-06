<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameMove extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'player_id',
        'position',
        'correct_answer',
        'question_id'
    ];

    protected $casts = [
        'correct_answer' => 'boolean'
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}