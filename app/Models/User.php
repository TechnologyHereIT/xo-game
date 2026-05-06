<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'last_seen',
        'avatar',
        'country', // إضافة الحقل الجديد
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = [
        'last_seen',   // ← أضفه هنا
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_seen' => 'datetime',
        ];
    }

    public function games()
    {
        return $this->hasOne(Game::class,'player1_id');
    }

    public function player()
    {
        return $this->hasOne(Player::class);
    }

    public function gamesAsPlayer1()
    {
        return $this->hasManyThrough(Game::class, Player::class, 'user_id', 'player1_id');
    }

    public function gamesAsPlayer2()
    {
        return $this->hasManyThrough(Game::class, Player::class, 'user_id', 'player2_id');
    }

    public function isOnline()
    {
        return $this->last_seen && $this->last_seen->gt(now()->subMinutes(5));
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar
            ? Storage::url($this->avatar)
            : 'https://ui-avatars.com/api/?name='.urlencode($this->name);
    }

    public function getGlobalRankAttribute()
    {
        return \DB::table('players')
            ->select('user_id')
            ->selectRaw('RANK() OVER (ORDER BY points DESC) as rank')
            ->whereNotNull('user_id')
            ->get()
            ->where('user_id', $this->id)
            ->first()
            ->rank ?? 'N/A';
    }

    // دالة للحصول على الترتيب حسب الدولة
    public function getCountryRankAttribute()
    {
        if (!$this->country) return 'N/A';

        return \DB::table('users')
            ->join('players', 'users.id', '=', 'players.user_id')
            ->where('users.country', $this->country)
            ->select('users.id')
            ->selectRaw('RANK() OVER (ORDER BY players.points DESC) as rank')
            ->get()
            ->where('id', $this->id)
            ->first()
            ->rank ?? 'N/A';
    }
}