<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\FetchesDataDragonVersion;
use App\Jobs\SyncChampionsJob;
use App\Models\Champion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ChampionController extends Controller
{
    use FetchesDataDragonVersion;

    public function index()
    {
        $latestVersion = $this->getLatestDataDragonVersion(); // Still needed for image paths
        $champions = Champion::all()->keyBy('id');

        if ($champions->isEmpty()) {
            // Optionally, dispatch the job if the table is empty
            SyncChampionsJob::dispatch();
            return view('champions', [
                'error' => 'No champion data found. A sync job has been dispatched. Please check back in a few minutes.',
                'tags' => collect(),
                'champions' => collect(),
                'version' => $this->getLatestDataDragonVersion(),
            ]);
        }

        // Manually decode JSON attributes because casting is not working reliably
        $champions->each(function ($champion) {
            $champion->tags = json_decode($champion->tags, true);
        });

        // Extract all unique tags
        $tags = $champions->flatMap(function ($champion) {
            return $champion['tags'];
        })->unique()->sort()->values();

        return view('champions', [
            'champions' => $champions,
            'version' => $latestVersion,
            'tags' => $tags,
        ]);
    }

    public function show($championId)
    {
        $latestVersion = $this->getLatestDataDragonVersion();

        $championData = Cache::remember("ddragon_champion_details_{$championId}_{$latestVersion}", 3600, function () use ($latestVersion, $championId) {
            $response = Http::get("https://ddragon.leagueoflegends.com/cdn/{$latestVersion}/data/en_US/champion/{$championId}.json");
            return $response->failed() ? null : $response->json()['data'][$championId];
        });

        if (!$championData) {
            abort(404, 'Champion not found.');
        }

        // --- START: Tooltip Processing Logic ---
        foreach ($championData['spells'] as &$spell) {
            if (empty($spell['tooltip'])) continue;

            $isComplex = (empty($spell['vars']) && preg_match('/{{ .* }}/', $spell['tooltip']));

            if ($isComplex) {
                $spell['processed_tooltip'] = $spell['description'];
            } else {
                $tooltip = $spell['tooltip'];
                $replacements = [];

                if (isset($spell['effectBurn']) && is_array($spell['effectBurn'])) {
                    foreach ($spell['effectBurn'] as $i => $effect) {
                        if ($effect !== null) {
                            $replacements["{{ e{$i} }}"] = $effect;
                        }
                    }
                }

                if (isset($spell['vars']) && is_array($spell['vars'])) {
                    foreach ($spell['vars'] as $var) {
                        if (empty($var['key'])) continue;
                        $key = $var['key'];
                        $link = $var['link'] ?? '';
                        $coeff = $var['coeff'] ?? '';
                        if (is_array($coeff)) {
                            $coeff = implode('/', $coeff);
                        }
                        $replacement = '';
                        if ($link === 'self') {
                            $replacement = $coeff;
                        } else if (!empty($link)) {
                            $formattedCoeff = is_numeric($coeff) ? ($coeff * 100) . '%' : $coeff;
                            $replacement = "(+" . $formattedCoeff . " " . strtoupper(str_replace('@', '', $link)) . ")";
                        }
                        $replacements["{{ {$key} }}"] = $replacement;
                    }
                }

                preg_match_all('/{{ (.*?) }}/', $tooltip, $matches);
                if (!empty($matches[1])) {
                    foreach ($matches[1] as $match) {
                        if (isset($spell[$match])) {
                            $replacements["{{ {$match} }}"] = $spell[$match];
                        }
                    }
                }

                $processedTooltip = str_replace(array_keys($replacements), array_values($replacements), $tooltip);
                $spell['processed_tooltip'] = $processedTooltip;
            }
        }
        unset($spell);
        // --- END: Tooltip Processing Logic ---

        return view('champion_detail', [
            'champion' => $championData,
            'version' => $latestVersion
        ]);
    }

    public function sync()
    {
        SyncChampionsJob::dispatch();
        return redirect()->route('champions.index')->with('status', 'Champion data sync has been dispatched! It will be processed in the background.');
    }
}