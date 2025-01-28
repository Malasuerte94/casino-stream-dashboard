<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GameSyncController extends Controller
{
    public function syncGames()
    {
        $csvFolder = storage_path('app/public/csv'); // Folder containing CSV files
        $csvFiles = glob($csvFolder . '/*.csv'); // Get all CSV files in the folder

        if (empty($csvFiles)) {
            return response()->json(['message' => 'No CSV files found in the folder.'], 404);
        }

        foreach ($csvFiles as $file) {
            $this->processCsv($file);
        }

        return response()->json(['message' => 'Games synced successfully.']);
    }

    private function processCsv($filePath)
    {
        $data = array_map('str_getcsv', file($filePath));
        $headers = array_shift($data); // Extract headers from the first row

        foreach ($data as $row) {
            $gameData = array_combine($headers, $row);

            // Update or create the game record
            Game::updateOrCreate(
                ['slug' => $gameData['slug']], // Use 'slug' as a unique identifier
                [
                    'name' => $gameData['name'],
                    'image' => $gameData['image'],
                ]
            );
        }
    }
}
