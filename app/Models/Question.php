<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'options',
        'correct_option',
        'difficulty',
        'category'
    ];

    protected $casts = [
        'options' => 'array'
    ];

    public function gameMoves()
    {
        return $this->hasMany(GameMove::class);
    }

    // دالة للحصول على الإجابة الصحيحة كنص
    public function getCorrectAnswerTextAttribute()
    {
        $options = $this->options;
        $correctIndex = $this->correct_option;
        
        return $options[$correctIndex] ?? null;
    }

    // التحقق إذا كانت الإجابة صحيحة
    public function isAnswerCorrect($selectedOption)
    {
        return $this->correct_option === $selectedOption;
    }
}