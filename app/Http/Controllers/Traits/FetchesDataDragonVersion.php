<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

trait FetchesDataDragonVersion
{
    public function getLatestDataDragonVersion(): string
    {
        return Cache::remember('ddragon_latest_version', 3600, function () { // Cache for 1 hour
            $versions = Http::get('https://ddragon.leagueoflegends.com/api/versions.json')->json();
            return $versions[0];
        });
    }

    public function getAllItems(string $version): ?array
    {
        return Cache::remember("ddragon_items_{$version}", 3600, function () use ($version) {
            $response = Http::get("https://ddragon.leagueoflegends.com/cdn/{$version}/data/en_US/item.json");
            return $response->failed() ? null : $response->json()['data'];
        });
    }
}
