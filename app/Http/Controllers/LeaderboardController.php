<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LeaderboardController extends Controller
{
    public function index()
    {
        $leaderboardData = $this->getLeaderboardData();

        if (isset($leaderboardData['error'])) {
            return view('leaderboard', ['error' => $leaderboardData['error']]);
        }

        return view('leaderboard', ['leaderboardData' => $leaderboardData]);
    }

    private function getLeaderboardData()
    {
        $apiKey = config('services.riot.key');

        if (!$apiKey) {
            return ['error' => 'Riot API key is not configured in config/services.php.'];
        }

        $response = Http::withHeaders([
            'X-Riot-Token' => $apiKey,
        ])->timeout(30)->get("https://br1.api.riotgames.com/lol/league/v4/challengerleagues/by-queue/RANKED_SOLO_5x5");

        if ($response->failed()) {
            return ['error' => 'An error occurred while fetching leaderboard data from the Riot API.'];
        }

        return $response->json();
    }
}
