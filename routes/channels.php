<?php

Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;   // خاصّ بالمستخدم نفسه فقط
});

Broadcast::channel('game.{gameId}', function ($user, $gameId) {
    // السماح فقط للاعبى اللعبة (player1 أو player2)
    $game = \App\Models\Game::find($gameId);
    if (!$game) return false;

    $player = $user->player;
    if (!$player) return false;

    return in_array($player->id, [$game->player1_id, $game->player2_id]);
});