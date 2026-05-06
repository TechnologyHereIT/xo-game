<?php

namespace App\Events;

use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GameUpdated implements ShouldBroadcast
{
    public function __construct(public $game) {}

    public function broadcastOn() {
        return new \Illuminate\Broadcasting\Channel('game.' . $this->game->id);
    }

    public function broadcastAs() {
        return 'game.updated';
    }
}