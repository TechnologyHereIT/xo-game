<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationResponded implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $userId;
    public $status;
    public $gameId;

    public function __construct($userId, $status, $gameId = null)
    {
        $this->userId = $userId;
        $this->status = $status;
        $this->gameId = $gameId;
    }

    public function broadcastOn()
    {
        return new Channel('user.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'invitation.responded';
    }
}
