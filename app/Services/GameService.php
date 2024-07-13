<?php

namespace App\Services;

use App\Models\Game;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class GameService
{

    public function createGame(array $questionIds, string $name)
    {
        $game = new Game();
        $game->name = $name;
        $game->owner_id = Auth::id();
        $game->save();

        $game->questions()->attach($questionIds);

        return $game;
    }

    public function getGameById(int $id): ?Game
    {
        $game = Game::where("id", $id)->with("questions")->first();
        $game->makeHidden(["owner_id", "entry_code", "updated_at","created_at"]);
        $game->questions->each(function ($question) {
            $question->makeHidden(["category_id", "created_at", "updated_at", "pivot"]);
        });
        return $game;
    }

    public function getGameByOwnerId()
    {
        $games = Game::where("owner_id", Auth::id())->get();

        $games->each(function ($game) {
            $game->makeHidden(["entry_code", "updated_at", "owner_id"]);
        });

        return $games;
    }
}
