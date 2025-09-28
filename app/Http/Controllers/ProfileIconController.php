<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\FetchesDataDragonVersion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ProfileIconController extends Controller
{
    use FetchesDataDragonVersion;

    public function index()
    {
        $latestVersion = $this->getLatestDataDragonVersion();

        $allIcons = Cache::remember("ddragon_profileicons_{$latestVersion}", 3600, function () use ($latestVersion) {
            $response = Http::get("https://ddragon.leagueoflegends.com/cdn/{$latestVersion}/data/en_US/profileicon.json");
            return $response->failed() ? null : $response->json()['data'];
        });

        if (!$allIcons) {
            return view('profile-icons', ['error' => 'Could not retrieve profile icon data.']);
        }

        // Manually paginate the array of icons
        $perPage = 96; // A good number for a 16-column grid
        $currentPage = request()->get('page', 1);
        $iconsCollection = collect($allIcons);

        $currentPageItems = $iconsCollection->slice(($currentPage - 1) * $perPage, $perPage)->all();

        $paginatedIcons = new LengthAwarePaginator(
            $currentPageItems,
            $iconsCollection->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );

        return view('profile-icons', [
            'icons' => $paginatedIcons,
            'version' => $latestVersion,
        ]);
    }
}
