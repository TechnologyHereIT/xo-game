<?php

namespace App\Events;

use App\Models\Game;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameRequestSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Game  $game,
        public User  $fromUser,
        public User  $toUser
    ) {}

    /**
     * القناة التي سيتم البث عليها
     */
    public function broadcastOn(): Channel
    {
        return new PrivateChannel('user.' . $this->toUser->id);
    }

    /**
     * اسم الحدث كما سيصل إلى الجانب المستمع
     */
    public function broadcastAs(): string
    {
        return 'game.request';
    }

    /**
     * البيانات المرسلة مع الحدث
     */
    public function broadcastWith(): array
    {
        return [
            'game'     => $this->game->only(['id', 'game_type', 'status']),
            'fromUser' => $this->fromUser->only(['id', 'name', 'avatar']),
            'toUser'   => $this->toUser->only(['id', 'name']),
        ];
    }
}