<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_type', 'player1_id', 'player2_id', 'status', 'current_turn', 
        'board', 'winner', 'tournament_id', 'speed_round_activated',
        'speed_round_used', 'player1_score', 'player2_score', 'round_number',
        'player1_powerups', 'player2_powerups', 'completed_at'
    ];

    protected $casts = [
        'board' => 'array',
        'speed_round_activated' => 'boolean',
        'speed_round_used' => 'boolean',
        'player1_score' => 'integer',
        'player2_score' => 'integer',
        'round_number' => 'integer',
        'player1_powerups' => 'array',
        'player2_powerups' => 'array',
        'completed_at' => 'datetime'
    ];

    public function player1()
    {
        return $this->belongsTo(Player::class, 'player1_id');
    }

    public function player2()
    {
        return $this->belongsTo(Player::class, 'player2_id');
    }

    public function moves()
    {
        return $this->hasMany(GameMove::class);
    }

    public function tournament()
    {
        return $this->belongsTo(Tournament::class);
    }

    public function invitation()
    {
        return $this->belongsTo(GameInvitation::class);
    }
    /**
     * الحصول على مصفوفة اللوحة مع معالجة الأخطاء
     */
    public function getBoardArrayAttribute()
    {
        if (is_array($this->board)) {
            return $this->board;
        }
        
        if (is_string($this->board)) {
            $decoded = json_decode($this->board, true);
            return is_array($decoded) ? $decoded : array_fill(0, 9, null);
        }
        
        return array_fill(0, 9, null);
    }

    /**
     * التحقق من وجود فائز - نسخة محسنة
     */
    public function checkWinner()
    {
        $board = $this->getBoardArrayAttribute();
        
        // ✅ التأكد من أن اللوحة مصفوفة صالحة
        if (!is_array($board) || count($board) !== 9) {
            return null;
        }

        $winningCombinations = [
            [0, 1, 2], [3, 4, 5], [6, 7, 8], // صفوف
            [0, 3, 6], [1, 4, 7], [2, 5, 8], // أعمدة
            [0, 4, 8], [2, 4, 6] // أقطار
        ];

        foreach ($winningCombinations as $combination) {
            $a = $board[$combination[0]] ?? null;
            $b = $board[$combination[1]] ?? null;
            $c = $board[$combination[2]] ?? null;

            // ✅ التحقق من أن جميع الخلايا ليست فارغة ومتساوية
            if ($a !== null && $a === $b && $a === $c) {
                return $a; // إرجاع 'X' أو 'O'
            }
        }

        return null;
    }

    /**
     * التحقق إذا كانت اللوحة ممتلئة
     */
    public function isBoardFull()
    {
        $board = $this->getBoardArrayAttribute();
        return !in_array(null, $board, true);
    }

    /**
     * الحصول على الخلايا الفارغة
     */
    public function getRemainingCells()
    {
        $board = $this->getBoardArrayAttribute();
        $remaining = [];
        foreach ($board as $index => $cell) {
            if ($cell === null) {
                $remaining[] = $index;
            }
        }
        return $remaining;
    }

    /**
     * التحقق إذا كان دور لاعب معين
     */
    public function isPlayerTurn($playerId)
    {
        if ($this->current_turn === 'player1' && $this->player1_id == $playerId) {
            return true;
        }
        if ($this->current_turn === 'player2' && $this->player2_id == $playerId) {
            return true;
        }
        return false;
    }

    /**
     * التحقق إذا كانت اللعبة ضد الكمبيوتر
     */
    public function isAgainstComputer()
    {
        return $this->game_type === 'computer' && $this->player2_id === null;
    }

    /**
     * الحصول على بطاقات لاعب معين
     */
    public function getPlayerPowerUps($playerId)
    {
        if ($this->player1_id == $playerId) {
            return $this->player1_powerups ?? $this->getDefaultPowerUps();
        }
        
        if ($this->player2_id == $playerId) {
            return $this->player2_powerups ?? $this->getDefaultPowerUps();
        }
        
        return [];
    }

    /**
     * تحديث بطاقات لاعب معين
     */
    public function updatePlayerPowerUps($playerId, $powerUps)
    {
        if ($this->player1_id == $playerId) {
            $this->player1_powerups = $powerUps;
        } elseif ($this->player2_id == $playerId) {
            $this->player2_powerups = $powerUps;
        }
        
        $this->save();
    }

    /**
     * البطاقات الافتراضية
     */
    private function getDefaultPowerUps()
    {
        return [
            'double_move' => [
                'name' => 'حركة مزدوجة',
                'used' => false,
                'icon' => '⚡',
                'cost' => 10,
                'description' => 'العب مرتين متتاليتين'
            ],
            'block_opponent' => [
                'name' => 'حجب الخصم',
                'used' => false,
                'icon' => '🚫',
                'cost' => 5,
                'description' => 'احجب دور الخصم التالي'
            ],
            'free_placement' => [
                'name' => 'وضع حر',
                'used' => false,
                'icon' => '🎯',
                'cost' => 10,
                'description' => 'ضع علامتك في أي مكان'
            ],
            'shuffle_board' => [
                'name' => 'تبديل اللوحة',
                'used' => false,
                'icon' => '🔀',
                'cost' => 7,
                'description' => 'بدل مواقع العلامات'
            ]
        ];
    }

    /**
     * التحقق إذا كانت اللعبة منتهية
     */
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    /**
     * إنهاء اللعبة مع تحديث الإحصائيات
     */
    public function endGame($winner = null)
    {
        $this->winner = $winner;
        $this->status = 'completed';
        $this->completed_at = now();
        $this->save();
    }

    /**
     * الحصول على رمز اللاعب
     */
    public function getPlayerSymbol($playerId)
    {
        return $this->player1_id == $playerId ? 'X' : 'O';
    }

    /**
     * الحصول على الخصم
     */
    public function getOpponent($playerId)
    {
        if ($this->player1_id == $playerId) {
            return $this->player2;
        }
        return $this->player1;
    }

    public function opponentNameFor($user)
    {
        if ($this->player1 && $this->player1->user && $this->player1->user->is($user)) {
            return $this->player2 && $this->player2->user ? $this->player2->user->name : 'الكمبيوتر';
        }
        return $this->player1 && $this->player1->user ? $this->player1->user->name : 'غير معروف';
    }

    /* شارة النتيجة */
    public function resultBadgeFor($user)
    {
        $symbol = ($this->player1 && $this->player1->user && $this->player1->user->is($user)) ? 'X' : 'O';
        if (!$this->winner) {
            return ['text'=>'تعادل','class'=>'bg-yellow-100 text-yellow-800'];
        }
        if ($this->winner === $symbol) {
            return ['text'=>'فوز','class'=>'bg-green-100 text-green-800'];
        }
        return ['text'=>'خسارة','class'=>'bg-red-100 text-red-800'];
    }

    /**
     * ✅ دالة جديدة: التحقق من تأثيرات البطاقات قبل الحركة
     */
    public function checkPowerUpEffects($playerId)
    {
        $effects = [
            'blocked' => false,
            'double_move' => false
        ];

        // التحقق من الحجب
        $blockedPlayer = cache()->get("blocked_{$this->id}");
        if ($blockedPlayer && $blockedPlayer == $playerId) {
            $effects['blocked'] = true;
            cache()->forget("blocked_{$this->id}");
            
            // تخطي دور اللاعب المحجوب
            $this->current_turn = $this->current_turn === 'player1' ? 'player2' : 'player1';
            $this->save();
        }

        // التحقق من الحركة المزدوجة
        $doubleMovePlayer = cache()->get("double_move_{$this->id}");
        if ($doubleMovePlayer && $doubleMovePlayer == $playerId) {
            $effects['double_move'] = true;
            cache()->forget("double_move_{$this->id}");
            
            // إبقاء الدور مع نفس اللاعب
            $this->current_turn = $this->current_turn; // يبقى كما هو
            $this->save();
        }

        return $effects;
    }
}