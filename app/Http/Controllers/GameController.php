<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    // Fetch all games
    public function index()
    {
        $games = Game::all();
        return response()->json(['games' => $games], 200);
    }

    // Store a new game
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:games',
            'image' => 'required|string|max:255',
        ]);

        $game = Game::create($validatedData);
        return response()->json(['game' => $game, 'message' => 'Game created successfully'], 201);
    }

    // Fetch a single game
    public function show(Game $game)
    {
        return response()->json(['game' => $game], 200);
    }

    // Update a game
    public function update(Request $request, Game $game)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:games,slug,' . $game->id,
            'image' => 'sometimes|required|string|max:255',
        ]);

        $game->update($validatedData);
        return response()->json(['game' => $game, 'message' => 'Game updated successfully'], 200);
    }

    // Delete a game
    public function destroy(Game $game)
    {
        $game->delete();
        return response()->json(['message' => 'Game deleted successfully'], 200);
    }
}
