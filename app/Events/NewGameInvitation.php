<?php

namespace App\Events;

use App\Models\GameInvitation;
use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewGameInvitation implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $invitation;

    public function __construct(GameInvitation $invitation)
    {
        $this->invitation = $invitation;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->invitation->to_user_id);
    }

    public function broadcastWith()
    {
        return [
            'invitation' => [
                'id' => $this->invitation->id,
                'from_user' => [
                    'id' => $this->invitation->fromUser->id,
                    'name' => $this->invitation->fromUser->name,
                    'avatar' => $this->invitation->fromUser->avatar,
                ],
                'game_type' => $this->invitation->game_type,
                'time_limit' => $this->invitation->time_limit,
                'message' => $this->invitation->message,
                'expires_at' => $this->invitation->expires_at,
                'time_remaining' => $this->invitation->time_remaining
            ]
        ];
    }
}