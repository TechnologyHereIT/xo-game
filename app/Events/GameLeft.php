<?php

namespace App\Events;

use App\Models\Game;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GameLeft implements ShouldBroadcast
{
    public function __construct(public Game $game, public User $user) {}

    public function broadcastOn()
    {
        return new Channel('game.' . $this->game->id);
    }

    public function broadcastAs()
    {
        return 'game.left';
    }
}