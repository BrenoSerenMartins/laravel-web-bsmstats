<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\FetchesDataDragonVersion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class SummonerSpellController extends Controller
{
    use FetchesDataDragonVersion;

    public function index()
    {
        $latestVersion = $this->getLatestDataDragonVersion();

        $spells = Cache::remember("ddragon_summonerspells_{$latestVersion}", 3600, function () use ($latestVersion) {
            $response = Http::get("https://ddragon.leagueoflegends.com/cdn/{$latestVersion}/data/en_US/summoner.json");
            return $response->failed() ? null : $response->json()['data'];
        });

        if (!$spells) {
            return view('summoner-spells', ['error' => 'Could not retrieve summoner spell data.']);
        }

        // Game mode name mapping
        $modeTranslations = [
            'CLASSIC' => 'Clássico (SR)',
            'ARAM' => 'ARAM',
            'URF' => 'URF',
            'TUTORIAL' => 'Tutorial',
            'ONEFORALL' => 'Um por Todos',
            'ASCENSION' => 'Ascensão',
            'FIRSTBLOOD' => 'Primeiro Abate',
            'KINGPORO' => 'Lenda do Rei Poro',
            'CHERRY' => 'Arena',
            'NEXUSBLITZ' => 'Nexus Blitz',
            'ARSR' => 'ARSR',
            'DOOMBOTSTEEMO' => 'Bots do Mal',
            'ODYSSEY' => 'Odisseia',
            'PROJECT' => 'PROJETO',
            'STARGUARDIAN' => 'Guardiãs Estelares',
        ];

        // Extract all unique game modes and translate them
        $modes = collect($spells)->flatMap(function ($spell) {
            return $spell['modes'];
        })->unique()->sort()->values()->mapWithKeys(function ($mode) use ($modeTranslations) {
            return [$mode => $modeTranslations[$mode] ?? $mode];
        });

        return view('summoner-spells', [
            'spells' => $spells,
            'version' => $latestVersion,
            'modes' => $modes,
        ]);
    }
}
