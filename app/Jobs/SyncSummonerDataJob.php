<?php

namespace App\Jobs;

use App\Models\GameMatch;
use App\Models\Summoner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncSummonerDataJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $puuid, public string $gameName, public string $tagLine)
    {
    }

    public function handle(): void
    {
        Log::info("SyncSummonerDataJob started for PUUID: {$this->puuid}");
        $apiKey = config('services.riot.key');
        if (!$apiKey) {
            Log::error('Riot API key is not configured for SyncSummonerDataJob.');
            return;
        }

        // 1. Fetch and update summoner data
        $summonerResponse = Http::withHeaders(['X-Riot-Token' => $apiKey])->timeout(30)
            ->get("https://br1.api.riotgames.com/lol/summoner/v4/summoners/by-puuid/{$this->puuid}");

        if ($summonerResponse->successful()) {
            $summonerData = $summonerResponse->json();
            Log::info('Riot API Response for Summoner:', $summonerData);

            Summoner::updateOrCreate(
                ['puuid' => $this->puuid],
                [
                    'gameName' => $this->gameName,
                    'tagLine' => $this->tagLine,
                    'summonerId' => $summonerData['id'] ?? null,
                    'accountId' => $summonerData['accountId'] ?? null,
                    'profileIconId' => $summonerData['profileIconId'] ?? 1,
                    'summonerLevel' => $summonerData['summonerLevel'] ?? 1,
                    'revisionDate' => $summonerData['revisionDate'] ?? now()->getTimestamp() * 1000,
                ]
            );
        }

        // 2. Fetch match history IDs
        $matchIdsResponse = Http::withHeaders(['X-Riot-Token' => $apiKey])->timeout(30)
            ->get("https://americas.api.riotgames.com/lol/match/v5/matches/by-puuid/{$this->puuid}/ids?start=0&count=10");

        if ($matchIdsResponse->failed()) {
            Log::error("Failed to fetch match IDs for PUUID: {$this->puuid}");
            return;
        }
        $matchIds = $matchIdsResponse->json();

        // 3. Fetch and store new matches
        foreach ($matchIds as $matchId) {
            if (GameMatch::where('id', $matchId)->exists()) {
                continue; // Skip if match already in DB
            }

            $matchResponse = Http::withHeaders(['X-Riot-Token' => $apiKey])->timeout(30)
                ->get("https://americas.api.riotgames.com/lol/match/v5/matches/{$matchId}");

            if ($matchResponse->successful()) {
                GameMatch::create([
                    'id' => $matchId,
                    'puuid' => $this->puuid,
                    'data' => json_encode($matchResponse->json()),
                ]);
            }
            usleep(100000); // Respect rate limits
        }

        Log::info("SyncSummonerDataJob finished for PUUID: {$this->puuid}");
    }
}