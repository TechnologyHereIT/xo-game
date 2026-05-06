<?php
namespace App\Services;

use App\Models\Game;

class AIComputerService
{
    public function makeMove(Game $game): int
    {
        $board = $this->getBoardArray($game);
        $best  = $this->minimax($board, 0, true, -INF, INF);
        return $best['index'];
    }

    private function minimax($board, $depth, $maximizing, $alpha, $beta)
    {
        $winner = $this->checkWinnerArr($board);
        if ($winner === 'O') return ['score'=> 10 - $depth];
        if ($winner === 'X') return ['score'=> -10 + $depth];
        if (!in_array('', $board)) return ['score'=>0];

        if ($maximizing){
            $best = ['score'=>-INF];
            foreach ($this->available($board) as $i){
                $board[$i] = 'O';
                $val = $this->minimax($board,$depth+1,false,$alpha,$beta);
                $val['index'] = $i;
                $best = $val['score'] > $best['score'] ? $val : $best;
                $alpha = max($alpha,$best['score']);
                $board[$i] = '';
                if ($beta <= $alpha) break;
            }
            return $best;
        }else{
            $best = ['score'=>INF];
            foreach ($this->available($board) as $i){
                $board[$i] = 'X';
                $val = $this->minimax($board,$depth+1,true,$alpha,$beta);
                $val['index'] = $i;
                $best = $val['score'] < $best['score'] ? $val : $best;
                $beta = min($beta,$best['score']);
                $board[$i] = '';
                if ($beta <= $alpha) break;
            }
            return $best;
        }
    }

    /* Helpers */
    private function getBoardArray(Game $game): array
    {
        $board = array_fill(0,9,'');
        foreach ($game->moves()->where('is_correct',true)->get() as $m){
            $symbol = $game->users()->find($m->user_id)->pivot->symbol;
            $board[$m->position] = $symbol;
        }
        return $board;
    }
    private function available($board){ return array_keys($board, ''); }
    private function checkWinnerArr($board)
    {
        $lines = [[0,1,2],[3,4,5],[6,7,8],[0,3,6],[1,4,7],[2,5,8],[0,4,8],[2,4,6]];
        foreach ($lines as [$a,$b,$c]){
            if ($board[$a] && $board[$a]===$board[$b] && $board[$a]===$board[$c]) return $board[$a];
        }
        return null;
    }
}