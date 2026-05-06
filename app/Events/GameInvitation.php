<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GameInvitation implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $invitation;

    public function __construct($invitation)
    {
        $this->invitation = $invitation;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->invitation['to_user_id']);
    }

    public function broadcastAs()
    {
        return 'game.invitation';
    }

    public function broadcastWith()
    {
        return [
            'invitation' => $this->invitation
        ];
    }
}