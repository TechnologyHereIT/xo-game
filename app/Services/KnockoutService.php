<?php
namespace App\Services;

use App\Models\User;
use App\Models\Game;
use App\Models\KnockoutBracket;

class KnockoutService
{
    public function generateBracket(Collection $users)
    {
        // نكمل إلى 32 = 30 + 2 byes
        $byes = 32 - $users->count();
        $players = $users->shuffle()->values();

        for($i=0;$i<$byes;$i++){
            $players->push(User::factory()->create(['name'=>'Bye '.($i+1)]));
        }

        $round = 1;
        while($players->count()>1){
            for($i=0;$i<$players->count();$i+=2){
                $game = Game::create(['type'=>'knockout','status'=>'waiting']);
                $game->users()->attach([$players[$i]->id=>['symbol'=>'X'], $players[$i+1]->id=>['symbol'=>'O']]);
                KnockoutBracket::create([
                    'game_id'=>$game->id,
                    'round_no'=>$round,
                    'player1_user_id'=>$players[$i]->id,
                    'player2_user_id'=>$players[$i+1]->id,
                ]);
            }
            // المتأهلون للجولة التالية
            $players = collect(); // سيتم ملؤهم لاحقًا عند انتهاء المباريات
            $round++;
        }
    }

    public function advanceWinner(Game $game)
    {
        $bracket = KnockoutBracket::where('game_id',$game->id)->first();
        $bracket->update(['winner_user_id'=>$game->winner_user_id]);

        // إذا اكتملت جميع مباريات الجولة ننشئ الجولة التالية
        $currentRound = $bracket->round_no;
        $nextRound = $currentRound + 1;
        $winners = KnockoutBracket::where('round_no',$currentRound)
                                  ->whereNotNull('winner_user_id')
                                  ->pluck('winner_user_id');

        if($winners->count() % 2 === 0){
            for($i=0;$i<$winners->count();$i+=2){
                $game = Game::create(['type'=>'knockout','status'=>'waiting']);
                $game->users()->attach([$winners[$i]=>['symbol'=>'X'], $winners[$i+1]=>['symbol'=>'O']]);
                KnockoutBracket::create([
                    'game_id'=>$game->id,
                    'round_no'=>$nextRound,
                    'player1_user_id'=>$winners[$i],
                    'player2_user_id'=>$winners[$i+1],
                ]);
            }
        }
    }
}