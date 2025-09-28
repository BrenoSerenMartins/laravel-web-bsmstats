<?php

namespace App\Jobs;

use App\Http\Controllers\Traits\FetchesDataDragonVersion;
use App\Models\Champion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncChampionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, FetchesDataDragonVersion;

    public function handle(): void
    {
        Log::info('SyncChampionsJob started.');
        $latestVersion = $this->getLatestDataDragonVersion();
        
        $response = Http::get("https://ddragon.leagueoflegends.com/cdn/{$latestVersion}/data/en_US/champion.json");

        if ($response->failed()) {
            Log::error('Failed to fetch champion summary for SyncChampionsJob.');
            return;
        }

        $champions = $response->json()['data'];
        Log::info('Found ' . count($champions) . ' champions. Syncing with database...');

        foreach ($champions as $championData) {
            Champion::updateOrCreate(
                ['id' => $championData['id']],
                [
                    'key' => $championData['key'],
                    'name' => $championData['name'],
                    'title' => $championData['title'],
                    'blurb' => $championData['blurb'],
                    'info' => json_encode($championData['info']),
                    'image' => json_encode($championData['image']),
                    'tags' => json_encode($championData['tags']),
                    'partype' => $championData['partype'],
                    'stats' => json_encode($championData['stats']),
                    // Detailed data will be fetched on demand from the show method
                    'spells' => json_encode($championData['spells'] ?? []),
                    'passive' => json_encode($championData['passive'] ?? []),
                ]
            );
        }
        Log::info('SyncChampionsJob finished successfully.');
    }
}