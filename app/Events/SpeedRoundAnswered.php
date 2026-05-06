<?php

namespace App\Events;

use App\Models\Game;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SpeedRoundAnswered implements ShouldBroadcast
{
    public $game;
    public $user;

    public function __construct(Game $game, User $user)
    {
        $this->game = $game;
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->game->id);
    }

    public function broadcastAs()
    {
        return 'speedround.answered';
    }
}