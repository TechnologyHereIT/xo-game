<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Game;
use App\Services\GameService;

class AdminGameBoard extends Component
{
    public Game $game;
    public array $board;

    protected $listeners = ['echo:game.{game.id},GameUpdated' => 'refreshBoard'];

    public function refreshBoard($payload)
    {
        $this->board = $payload['board'];
    }

    public function render()
    {
        return view('livewire.admin-game-board');
    }
}