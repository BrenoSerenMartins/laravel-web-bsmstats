<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\FetchesDataDragonVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MatchController extends Controller
{
    use FetchesDataDragonVersion;

    public function show($matchId)
    {
        $latestVersion = $this->getLatestDataDragonVersion();

        $matchData = $this->getMatchData($matchId);

        if (isset($matchData['error'])) {
            return view('match', ['error' => $matchData['error']]);
        }

        return view('match', [
            'matchData' => $matchData,
            'version' => $latestVersion
        ]);
    }

    private function getMatchData($matchId)
    {
        $apiKey = config('services.riot.key');

        if (!$apiKey) {
            return ['error' => 'Riot API key is not configured in config/services.php.'];
        }

        $response = Http::withHeaders([
            'X-Riot-Token' => $apiKey,
        ])->timeout(30)->get("https://americas.api.riotgames.com/lol/match/v5/matches/{$matchId}");

        if ($response->failed()) {
            return ['error' => 'An error occurred while fetching match data from the Riot API.'];
        }

        return $response->json();
    }
}