<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameInvitation extends Model
{
    use HasFactory;


    protected $fillable = [
        'from_user_id',
        'to_user_id', 
        'status',
        'game_type',
        'time_limit',
        'message',
        'expires_at'
    ];


    protected $casts = [
        'expires_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    protected $attributes = [
        'status' => 'pending',
        'game_type' => 'classic',
        'time_limit' => 300, // 5 دقائق افتراضياً
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function game()
    {
        return $this->hasOne(Game::class, 'invitation_id');
    }

    public function getCanBeAcceptedAttribute()
    {
        return $this->is_active && !$this->is_expired;
    }

    public function accept()
    {
        $this->update(['status' => 'accepted']);
        
        // هنا يمكنك إنشاء لعبة جديدة
        $game = Game::create([
            'player1_id' => $this->from_user_id,
            'player2_id' => $this->to_user_id,
            'invitation_id' => $this->id,
            'game_type' => $this->game_type,
            'time_limit' => $this->time_limit,
            'status' => 'active'
        ]);

        return $game;
    }

    public function reject()
    {
        return $this->update(['status' => 'rejected']);
    }

    public function cancel()
    {
        return $this->update(['status' => 'cancelled']);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invitation) {
            if (empty($invitation->expires_at)) {
                $invitation->expires_at = now()->addMinutes(10); // 10 دقائق افتراضياً
            }
        });

        static::updated(function ($invitation) {
            // إذا تم قبول الدعوة، نقوم بتحديث جميع الدعوات الأخرى لنفس اللاعبين إلى منتهية
            if ($invitation->status === 'accepted') {
                static::where('from_user_id', $invitation->from_user_id)
                    ->orWhere('to_user_id', $invitation->to_user_id)
                    ->where('id', '!=', $invitation->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'expired']);
            }
        });
    }

    public static function getActiveInvitationsForUser($userId)
    {
        return static::where(function($query) use ($userId) {
            $query->where('from_user_id', $userId)
                  ->orWhere('to_user_id', $userId);
        })
        ->where('status', 'pending')
        ->where('expires_at', '>', now())
        ->with(['fromUser', 'toUser'])
        ->get();
    }

    public static function getReceivedInvitations($userId)
    {
        return static::where('to_user_id', $userId)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->with('fromUser')
            ->get();
    }

    public static function getSentInvitations($userId)
    {
        return static::where('from_user_id', $userId)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->with('toUser')
            ->get();
    }

    public static function createInvitation($fromUserId, $toUserId, $data = [])
    {
        // التحقق من عدم وجود دعوة نشطة بين اللاعبين
        $existingInvitation = static::where(function($query) use ($fromUserId, $toUserId) {
            $query->where('from_user_id', $fromUserId)
                  ->where('to_user_id', $toUserId)
                  ->orWhere('from_user_id', $toUserId)
                  ->where('to_user_id', $fromUserId);
        })
        ->where('status', 'pending')
        ->where('expires_at', '>', now())
        ->first();

        if ($existingInvitation) {
            throw new \Exception('يوجد دعوة نشطة بالفعل بين اللاعبين');
        }

        // التحقق من أن المستخدم لا يرسل دعوة لنفسه
        if ($fromUserId === $toUserId) {
            throw new \Exception('لا يمكن إرسال دعوة لنفسك');
        }

        return static::create(array_merge([
            'from_user_id' => $fromUserId,
            'to_user_id' => $toUserId,
        ], $data));
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'pending')
                    ->where('expires_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'pending')
              ->where('expires_at', '<=', now());
        })->orWhere('status', 'expired');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'في انتظار الرد',
            'accepted' => 'مقبولة',
            'rejected' => 'مرفوضة', 
            'cancelled' => 'ملغاة',
            'expired' => 'منتهية الصلاحية'
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'pending' => 'yellow',
            'accepted' => 'green',
            'rejected' => 'red',
            'cancelled' => 'gray',
            'expired' => 'orange'
        ];

        return $colors[$this->status] ?? 'gray';
    }

    // خاصية للحصول على الوقت المتبقي
    public function getTimeRemainingAttribute()
    {
        return $this->expires_at->diffForHumans();
    }

    // التحقق من انتهاء الصلاحية
    public function getIsExpiredAttribute()
    {
        return $this->expires_at->isPast();
    }

    // التحقق إذا كانت الدعوة نشطة
    public function getIsActiveAttribute()
    {
        return $this->status === 'pending' && !$this->is_expired;
    }
}

