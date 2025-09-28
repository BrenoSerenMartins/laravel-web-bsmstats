<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\FetchesDataDragonVersion;
use App\Jobs\SyncSummonerDataJob;
use App\Models\GameMatch;
use App\Models\Summoner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SummonerController extends Controller
{
    use FetchesDataDragonVersion;

    public function search(Request $request)
    {
        $gameName = $request->input('gameName');
        $tagLine = $request->input('tagLine');
        $queueType = $request->input('queueType', 'all');

        if (!$gameName || !$tagLine) {
            return view('summoner', ['error' => 'Please enter both a game name and a tag line.']);
        }

        $accountData = $this->getAccountByRiotId($gameName, $tagLine);
        if (isset($accountData['error'])) {
            return view('summoner', ['error' => $accountData['error']]);
        }
        $puuid = $accountData['puuid'];

        SyncSummonerDataJob::dispatch($puuid, $gameName, $tagLine);

        $summoner = Summoner::find($puuid);
        $matchesQuery = GameMatch::where('puuid', $puuid);

        if (!$summoner || $matchesQuery->count() === 0) {
            return view('summoner', [
                'error' => 'Summoner data is being synced. Please refresh in a moment.',
                'summonerData' => ['name' => $gameName, 'tagLine' => $tagLine, 'level' => '?', 'profileIconId' => 1],
                'aggregateStats' => $this->calculateAggregateStats(collect(), ''),
                'detailedMatches' => [],
                'masteryWithChampionData' => [],
                'version' => $this->getLatestDataDragonVersion(),
                'champions' => [],
                'summonerSpells' => [],
                'runes' => [],
                'puuid' => $puuid,
                'queueType' => $queueType,
            ]);
        }

        if ($queueType !== 'all') {
            $matchesQuery->whereJsonContains('data->info->gameMode', strtoupper($queueType));
        }

        $matches = $matchesQuery->latest()->take(10)->get();
        
        // Manually decode the JSON data from the models
        $detailedMatches = $matches->map(function ($match) {
            $matchData = json_decode($match->data, true);
            $matchData['info']['gameEndedAgo'] = Carbon::createFromTimestamp($matchData['info']['gameEndTimestamp'] / 1000)->diffForHumans();
            return $matchData;
        });

        $aggregateStats = $this->calculateAggregateStats($detailedMatches, $puuid);
        $championMastery = $this->getChampionMastery($puuid);
        $latestVersion = $this->getLatestDataDragonVersion();
        
        $champions = Cache::remember("ddragon_champions_{$latestVersion}", 3600, function () use ($latestVersion) {
            return Http::get("https://ddragon.leagueoflegends.com/cdn/{$latestVersion}/data/en_US/champion.json")->json()['data'] ?? null;
        });
        $summonerSpells = Cache::remember("ddragon_summonerspells_{$latestVersion}", 3600, function () use ($latestVersion) {
            return Http::get("https://ddragon.leagueoflegends.com/cdn/{$latestVersion}/data/en_US/summoner.json")->json()['data'] ?? null;
        });
        $runes = Cache::remember("ddragon_runes_{$latestVersion}", 3600, function () use ($latestVersion) {
            return Http::get("https://ddragon.leagueoflegends.com/cdn/{$latestVersion}/data/en_US/runesReforged.json")->json() ?? null;
        });

        $allItems = $this->getAllItems($latestVersion);

        $masteryWithChampionData = [];
        if ($champions && !empty($championMastery)) {
            $championsByKey = collect($champions)->keyBy('key');
            foreach ($championMastery as $mastery) {
                $championKey = (string) $mastery['championId'];
                if ($championsByKey->has($championKey)) {
                    $mastery['champion'] = $championsByKey->get($championKey);
                    $masteryWithChampionData[] = $mastery;
                }
            }
        }
        
        $viewData = [
            'name' => $summoner->gameName,
            'tagLine' => $summoner->tagLine,
            'level' => $summoner->summonerLevel,
            'profileIconId' => $summoner->profileIconId,
        ];

        return view('summoner', [
            'summonerData' => $viewData,
            'detailedMatches' => $detailedMatches,
            'aggregateStats' => $aggregateStats,
            'masteryWithChampionData' => $masteryWithChampionData,
            'version' => $latestVersion,
            'champions' => $champions,
            'summonerSpells' => $summonerSpells,
            'runes' => $runes,
            'allItems' => $allItems,
            'puuid' => $puuid,
            'queueType' => $queueType,
        ]);
    }

    private function getAccountByRiotId(string $gameName, string $tagLine)
    {
        $apiKey = config('services.riot.key');
        if (!$apiKey) return ['error' => 'Riot API key is not configured.'];

        $url = "https://americas.api.riotgames.com/riot/account/v1/accounts/by-riot-id/{$gameName}/{$tagLine}";
        return Cache::remember("riot_account_{$gameName}_{$tagLine}", 3600, function () use ($url, $apiKey) {
            $response = Http::withHeaders(['X-Riot-Token' => $apiKey])->timeout(30)->get($url);
            return $response->failed() ? ['error' => 'Could not find account with that Riot ID.'] : $response->json();
        });
    }

    private function getChampionMastery(string $puuid)
    {
        $apiKey = config('services.riot.key');
        if (!$apiKey) return [];

        $url = "https://br1.api.riotgames.com/lol/champion-mastery/v4/champion-masteries/by-puuid/{$puuid}/top?count=5";
        return Cache::remember("riot_mastery_{$puuid}", 3600, function () use ($url, $apiKey) {
            $response = Http::withHeaders(['X-Riot-Token' => $apiKey])->timeout(30)->get($url);
            return $response->failed() ? [] : $response->json();
        });
    }

    private function calculateAggregateStats(\Illuminate\Support\Collection $matches, string $puuid): array
    {
        if ($matches->isEmpty()) {
            return [
                'wins' => 0, 'losses' => 0, 'totalGames' => 0, 'winRate' => 0,
                'avgKda' => 0, 'avgKills' => 0, 'avgDeaths' => 0, 'avgAssists' => 0,
                'mostPlayed' => [], 'avgKpa' => 0, 'avgCsPerMin' => 0, 'avgGoldPerMin' => 0,
                'laneDistribution' => [],
            ];
        }

        $playerStats = $matches->map(function ($match) use ($puuid) {
            $playerParticipant = collect($match['info']['participants'])->firstWhere('puuid', $puuid);
            if (!$playerParticipant) return null;

            $teamId = $playerParticipant['teamId'];
            $teamKills = collect($match['info']['participants'])
                ->where('teamId', $teamId)
                ->sum('kills');
            
            $gameDurationInMinutes = $match['info']['gameDuration'] / 60;

            return [
                'win' => $playerParticipant['win'],
                'kills' => $playerParticipant['kills'],
                'deaths' => $playerParticipant['deaths'],
                'assists' => $playerParticipant['assists'],
                'championName' => $playerParticipant['championName'],
                'lane' => $playerParticipant['teamPosition'] ?? 'UNKNOWN',
                'kpa' => ($teamKills > 0) ? (($playerParticipant['kills'] + $playerParticipant['assists']) / $teamKills) * 100 : 0,
                'csPerMin' => $gameDurationInMinutes > 0 ? ($playerParticipant['totalMinionsKilled'] + $playerParticipant['neutralMinionsKilled']) / $gameDurationInMinutes : 0,
                'goldPerMin' => $gameDurationInMinutes > 0 ? $playerParticipant['goldEarned'] / $gameDurationInMinutes : 0,
            ];
        })->filter();

        $totalGames = $playerStats->count();
        if ($totalGames === 0) {
            return [
                'wins' => 0, 'losses' => 0, 'totalGames' => 0, 'winRate' => 0,
                'avgKda' => 0, 'avgKills' => 0, 'avgDeaths' => 0, 'avgAssists' => 0,
                'mostPlayed' => [], 'avgKpa' => 0, 'avgCsPerMin' => 0, 'avgGoldPerMin' => 0,
                'laneDistribution' => [],
            ];
        }

        $wins = $playerStats->where('win', true)->count();
        $totalKills = $playerStats->sum('kills');
        $totalDeaths = $playerStats->sum('deaths');
        $totalAssists = $playerStats->sum('assists');
        
        $championPlayCount = $playerStats->groupBy('championName')->map->count()->sortDesc();
        $laneCounts = $playerStats->groupBy('lane')->map->count();
        $laneDistribution = $laneCounts->map(function ($count) use ($totalGames) {
            return round(($count / $totalGames) * 100);
        });

        $losses = $totalGames - $wins;
        $winRate = ($totalGames > 0) ? round(($wins / $totalGames) * 100) : 0;
        $avgKda = ($totalDeaths > 0) ? ($totalKills + $totalAssists) / $totalDeaths : ($totalKills + $totalAssists);

        return [
            'wins' => $wins,
            'losses' => $losses,
            'totalGames' => $totalGames,
            'winRate' => $winRate,
            'avgKda' => round($avgKda, 2),
            'avgKills' => round($totalKills / $totalGames, 1),
            'avgDeaths' => round($totalDeaths / $totalGames, 1),
            'avgAssists' => round($totalAssists / $totalGames, 1),
            'mostPlayed' => $championPlayCount->take(3)->all(),
            'avgKpa' => round($playerStats->avg('kpa')),
            'avgCsPerMin' => round($playerStats->avg('csPerMin'), 1),
            'avgGoldPerMin' => round($playerStats->avg('goldPerMin')),
            'kdaPerMatch' => $playerStats->map(fn ($s) => ($s['deaths'] > 0) ? round(($s['kills'] + $s['assists']) / $s['deaths'], 2) : $s['kills'] + $s['assists'])->reverse()->values()->all(),
            'csPerMinPerMatch' => $playerStats->pluck('csPerMin')->reverse()->values()->all(),
            'goldPerMinPerMatch' => $playerStats->pluck('goldPerMin')->reverse()->values()->all(),
        ];
    }
}