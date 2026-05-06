<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'max_players',
        'current_players',
        'status',
        'start_date',
        'end_date',
        'winner_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function games()
    {
        return $this->hasMany(Game::class);
    }

    public function winner()
    {
        return $this->belongsTo(Player::class, 'winner_id');
    }

    public function participants()
    {
        return $this->belongsToMany(Player::class, 'tournament_participants')
                    ->withTimestamps();
    }

    private function getActiveTournaments()
    {
        // إذا ما عندك جدول البطولات، استخدم بيانات ثابتة
        return collect([
            (object)[
                'name' => 'كأس الأبطال',
                'status_text' => 'جاري',
                'status_color' => 'green',
                'participants_count' => 32,
                'current_round' => 'الجولة 4',
                'prize' => 5000,
                'time_remaining' => '2d 5h'
            ],
            (object)[
                'name' => 'تحدي النجوم', 
                'status_text' => 'قريب',
                'status_color' => 'yellow',
                'participants_count' => 64,
                'current_round' => 'يبدأ قريباً',
                'prize' => 8000,
                'time_remaining' => '1d 12h'
            ],
            (object)[
                'name' => 'ماراثون الذكاء',
                'status_text' => 'مفتوح', 
                'status_color' => 'blue',
                'participants_count' => 128,
                'current_round' => 'التسجيل مفتوح',
                'prize' => 12000,
                'time_remaining' => 'ينتهي التسجيل: 3d'
            ]
        ]);
    }
}