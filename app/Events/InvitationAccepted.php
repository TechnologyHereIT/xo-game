<?php

namespace App\Events;

use App\Models\GameInvitation;
use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvitationAccepted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $invitation;
    public $game;

    public function __construct(GameInvitation $invitation, Game $game)
    {
        $this->invitation = $invitation;
        $this->game = $game;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->invitation->from_user_id);
    }

    public function broadcastAs()
    {
        return 'invitation.accepted';
    }

    public function broadcastWith()
    {
        return [
            'invitation_id' => $this->invitation->id,
            'game_id' => $this->game->id,
            'message' => 'تم قبول دعوتك! جاري تحميل اللعبة...',
            'redirect_url' => '/game/' . $this->game->id
        ];
    }
}