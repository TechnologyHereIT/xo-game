<?php

namespace App\Events;

use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SpeedRoundActivated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function broadcastOn()
    {
        return new Channel('game.' . $this->game->id);
    }

    public function broadcastAs()
    {
        return 'speedround.activated';
    }

    public function broadcastWith()
    {
        return [
            'game_id' => $this->game->id,
            'speed_round_activated' => true,
            'message' => '🎯 جولة السرعة مفعلة!'
        ];
    }
}