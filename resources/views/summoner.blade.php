@extends('layouts.app')

@section('title', ($summonerData['name'] ?? 'Error') . ' - Summoner Details')

@section('content')
    @if (isset($error))
        <div class="bg-gray-800 rounded-lg p-8">
            <h1 class="text-4xl font-bold mb-4">Error</h1>
            <p class="text-red-500">{{ $error }}</p>
        </div>
    @else
        <!-- Summoner Header -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/profileicon/{{ $summonerData['profileIconId'] }}.png" alt="Profile Icon" class="w-24 h-24 rounded-full mr-4">
                <div>
                    <h1 class="text-4xl font-bold">{{ $summonerData['name'] }}<span class="text-3xl text-gray-500">#{{ $summonerData['tagLine'] }}</span></h1>
                    <p class="text-gray-400">Level {{ $summonerData['level'] }}</p>
                </div>
            </div>
            @if(!empty($masteryWithChampionData))
                <div class="text-right">
                    <p class="text-sm text-gray-400 mb-1">Main Champions</p>
                    <div class="flex items-end gap-2">
                        @if(isset($masteryWithChampionData[1]))
                            <a href="/champion/{{ $masteryWithChampionData[1]['champion']['id'] }}" class="opacity-75 hover:opacity-100 transition text-center">
                                <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/champion/{{ $masteryWithChampionData[1]['champion']['id'] }}.png" class="w-12 h-12 rounded-md">
                            </a>
                        @endif
                        @if(isset($masteryWithChampionData[0]))
                            <a href="/champion/{{ $masteryWithChampionData[0]['champion']['id'] }}" class="text-center">
                                <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/champion/{{ $masteryWithChampionData[0]['champion']['id'] }}.png" class="w-16 h-16 rounded-md border-2 border-yellow-400">
                            </a>
                        @endif
                        @if(isset($masteryWithChampionData[2]))
                            <a href="/champion/{{ $masteryWithChampionData[2]['champion']['id'] }}" class="opacity-75 hover:opacity-100 transition text-center">
                                <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/champion/{{ $masteryWithChampionData[2]['champion']['id'] }}.png" class="w-12 h-12 rounded-md">
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Stats Dashboard -->
        <div class="bg-gray-800 rounded-lg p-8 mb-8">
            <h2 class="text-2xl font-bold mb-4">Last {{ $aggregateStats['totalGames'] }} Games Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-center">
                <div class="md:col-span-1 flex flex-col items-center justify-center">
                    <div class="w-40 h-40 mb-2">
                        <canvas id="winLossChart"></canvas>
                    </div>
                    <p class="text-sm text-gray-400"><span class="font-bold text-blue-400">{{ $aggregateStats['wins'] }}W</span> / <span class="font-bold text-red-400">{{ $aggregateStats['losses'] }}L</span> ({{ $aggregateStats['winRate'] }}%)</p>
                </div>
                <div class="md:col-span-2 grid grid-cols-2 lg:grid-cols-3 gap-4 text-center">
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <p class="text-gray-400 text-sm">Avg. KDA</p>
                        <p class="text-3xl font-bold">{{ $aggregateStats['avgKda'] }}</p>
                    </div>
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <p class="text-gray-400 text-sm">KPA</p>
                        <p class="text-3xl font-bold">{{ $aggregateStats['avgKpa'] }}%</p>
                    </div>
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <p class="text-gray-400 text-sm">CS/min</p>
                        <p class="text-3xl font-bold">{{ $aggregateStats['avgCsPerMin'] }}</p>
                    </div>
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <p class="text-gray-400 text-sm">Gold/min</p>
                        <p class="text-3xl font-bold">{{ $aggregateStats['avgGoldPerMin'] }}</p>
                    </div>
                    <div class="bg-gray-700 p-4 rounded-lg col-span-2 lg:col-span-3">
                        <p class="text-gray-400 text-sm">Avg. Score</p>
                        <p class="text-xl font-bold"><span class="text-green-400">{{ $aggregateStats['avgKills'] }}</span> / <span class="text-red-400">{{ $aggregateStats['avgDeaths'] }}</span> / <span class="text-yellow-400">{{ $aggregateStats['avgAssists'] }}</span></p>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-8">
                <div class="lg:col-span-1 h-48"><canvas id="kdaChart"></canvas></div>
                <div class="lg:col-span-1 h-48"><canvas id="csChart"></canvas></div>
                <div class="lg:col-span-1 h-48"><canvas id="goldChart"></canvas></div>
            </div>
        </div>

        <!-- Detailed Match History -->
        <div class="bg-gray-800 rounded-lg p-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold">Match History</h2>
                <div class="flex items-center gap-2">
                    @php
                        $filters = ['all' => 'All', 'ranked' => 'Ranked', 'normal' => 'Normal', 'aram' => 'ARAM'];
                    @endphp
                    @foreach($filters as $key => $name)
                        <a href="{{ route('summoner.search', array_merge(request()->except('page'), ['gameName' => $summonerData['name'], 'tagLine' => $summonerData['tagLine'], 'queueType' => $key])) }}"
                           class="px-3 py-1.5 rounded-md text-xs font-bold {{ $queueType == $key ? 'bg-blue-500 text-white' : 'bg-gray-700 hover:bg-gray-600 text-white' }}">
                            {{ $name }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="space-y-2">
                @foreach ($detailedMatches as $match)
                    @php
                        $player = collect($match['info']['participants'])->firstWhere('puuid', $puuid);
                    @endphp
                    @if($player)
                        <div>
                            <div class="match-card-header flex items-center p-4 rounded-t-lg cursor-pointer {{ $player['win'] ? 'bg-blue-900/50' : 'bg-red-900/50' }}">
                                <div class="flex-shrink-0 w-24 text-center">
                                    <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/champion/{{ $player['championName'] }}.png" alt="{{ $player['championName'] }}" class="w-16 h-16 rounded-full mx-auto mb-1">
                                </div>
                                <div class="flex-shrink-0 w-24 text-center">
                                    <p class="text-lg font-bold">{{ $player['kills'] }} / <span class="text-red-500">{{ $player['deaths'] }}</span> / {{ $player['assists'] }}</p>
                                    <p class="text-xs text-gray-400">KDA</p>
                                </div>
                                <div class="flex-grow flex items-center justify-center gap-1 px-4">
                                    <div class="grid grid-cols-3 gap-1">
                                        @for ($i = 0; $i < 6; $i++)
                                            <div class="relative item-tooltip-container">
                                                @if($player["item{$i}"] !== 0 && isset($allItems[$player["item{$i}"]]))
                                                    <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/item/{{ $player["item{$i}"] }}.png" alt="Item" class="w-8 h-8 rounded-md bg-gray-700">
                                                    <div class="item-tooltip hidden absolute z-10 w-64 p-2 -top-10 left-10 bg-gray-950 text-white rounded-lg shadow-lg border border-gray-700 text-xs">
                                                        <p class="font-bold mb-1">{{ $allItems[$player["item{$i}"]]['name'] }}</p>
                                                        <p class="text-gray-400">{{ $allItems[$player["item{$i}"]]['plaintext'] }}</p>
                                                    </div>
                                                @else
                                                    <div class="w-8 h-8 rounded-md bg-gray-700/50"></div>
                                                @endif
                                            </div>
                                        @endfor
                                    </div>
                                    <div class="ml-2 relative item-tooltip-container">
                                        @if($player["item6"] !== 0 && isset($allItems[$player["item6"]]))
                                            <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/item/{{ $player["item6"] }}.png" alt="Trinket" class="w-8 h-8 rounded-md bg-gray-700">
                                            <div class="item-tooltip hidden absolute z-10 w-64 p-2 -top-10 left-10 bg-gray-950 text-white rounded-lg shadow-lg border border-gray-700 text-xs">
                                                <p class="font-bold mb-1">{{ $allItems[$player["item6"]]['name'] }}</p>
                                                <p class="text-gray-400">{{ $allItems[$player["item6"]]['plaintext'] }}</p>
                                            </div>
                                        @else
                                            <div class="w-8 h-8 rounded-md bg-gray-700/50"></div>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-shrink-0 w-32 text-right">
                                    <p class="font-bold {{ $player['win'] ? 'text-blue-400' : 'text-red-400' }}">{{ $player['win'] ? 'Victory' : 'Defeat' }}</p>
                                    <p class="text-xs text-gray-400">{{ $match['info']['gameMode'] }}</p>
                                    <p class="text-xs text-gray-400">{{ \Carbon\Carbon::createFromTimestamp($match['info']['gameEndTimestamp'] / 1000)->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="match-card-details hidden bg-gray-800 p-4 rounded-b-lg">
                                <div class="grid grid-cols-2 gap-6">
                                    <!-- Blue Team -->
                                    <div>
                                        <h4 class="text-lg font-bold text-blue-400 mb-2">Blue Team</h4>
                                        <table class="w-full text-xs text-left">
                                            <thead>
                                                <tr class="text-gray-400">
                                                    <th class="p-1">Champion</th>
                                                    <th class="p-1">KDA</th>
                                                    <th class="p-1">Damage</th>
                                                    <th class="p-1">Items</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-700">
                                                @foreach($match['info']['participants'] as $participant)
                                                    @if($participant['teamId'] == 100)
                                                        <tr>
                                                            <td class="p-1 flex items-center gap-2">
                                                                <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/champion/{{ $participant['championName'] }}.png" class="w-6 h-6 rounded-full">
                                                                <span class="truncate">{{ $participant['summonerName'] }}</span>
                                                            </td>
                                                            <td>{{ $participant['kills'] }}/{{ $participant['deaths'] }}/{{ $participant['assists'] }}</td>
                                                            <td>{{ number_format($participant['totalDamageDealtToChampions']) }}</td>
                                                            <td class="flex items-center gap-1 pt-1">
                                                                @for ($i = 0; $i <= 5; $i++)
                                                                    <div class="relative item-tooltip-container">
                                                                        @if($participant["item{$i}"] !== 0 && isset($allItems[$participant["item{$i}"]]))
                                                                            <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/item/{{ $participant["item{$i}"] }}.png" class="w-6 h-6 rounded-md">
                                                                            <div class="item-tooltip hidden absolute z-10 w-56 p-2 -top-10 left-0 bg-gray-950 text-white rounded-lg shadow-lg border border-gray-700 text-xs">
                                                                                <p class="font-bold mb-1">{{ $allItems[$participant["item{$i}"]]['name'] }}</p>
                                                                                <p class="text-gray-400">{{ $allItems[$participant["item{$i}"]]['plaintext'] }}</p>
                                                                            </div>
                                                                        @else
                                                                            <div class="w-6 h-6 rounded-md bg-gray-700/50"></div>
                                                                        @endif
                                                                    </div>
                                                                @endfor
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Red Team -->
                                    <div>
                                        <h4 class="text-lg font-bold text-red-400 mb-2">Red Team</h4>
                                        <table class="w-full text-xs text-left">
                                            <thead>
                                                <tr class="text-gray-400">
                                                    <th class="p-1">Champion</th>
                                                    <th class="p-1">KDA</th>
                                                    <th class="p-1">Damage</th>
                                                    <th class="p-1">Items</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-700">
                                                @foreach($match['info']['participants'] as $participant)
                                                    @if($participant['teamId'] == 200)
                                                        <tr>
                                                            <td class="p-1 flex items-center gap-2">
                                                                <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/champion/{{ $participant['championName'] }}.png" class="w-6 h-6 rounded-full">
                                                                <span class="truncate">{{ $participant['summonerName'] }}</span>
                                                            </td>
                                                            <td>{{ $participant['kills'] }}/{{ $participant['deaths'] }}/{{ $participant['assists'] }}</td>
                                                            <td>{{ number_format($participant['totalDamageDealtToChampions']) }}</td>
                                                            <td class="flex items-center gap-1 pt-1">
                                                                @for ($i = 0; $i <= 5; $i++)
                                                                    <div class="relative item-tooltip-container">
                                                                        @if($participant["item{$i}"] !== 0 && isset($allItems[$participant["item{$i}"]]))
                                                                            <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/item/{{ $participant["item{$i}"] }}.png" class="w-6 h-6 rounded-md">
                                                                            <div class="item-tooltip hidden absolute z-10 w-56 p-2 -top-10 left-0 bg-gray-950 text-white rounded-lg shadow-lg border border-gray-700 text-xs">
                                                                                <p class="font-bold mb-1">{{ $allItems[$participant["item{$i}"]]['name'] }}</p>
                                                                                <p class="text-gray-400">{{ $allItems[$participant["item{$i}"]]['plaintext'] }}</p>
                                                                            </div>
                                                                        @else
                                                                            <div class="w-6 h-6 rounded-md bg-gray-700/50"></div>
                                                                        @endif
                                                                    </div>
                                                                @endfor
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Accordion for match details
    const matchHeaders = document.querySelectorAll('.match-card-header');
    matchHeaders.forEach(header => {
        header.addEventListener('click', () => {
            const details = header.nextElementSibling;
            details.classList.toggle('hidden');
        });
    });

    // Item tooltips
    const itemContainers = document.querySelectorAll('.item-tooltip-container');
    itemContainers.forEach(container => {
        const tooltip = container.querySelector('.item-tooltip');
        if(tooltip) {
            container.addEventListener('mouseenter', () => { tooltip.classList.remove('hidden'); });
            container.addEventListener('mouseleave', () => { tooltip.classList.add('hidden'); });
        }
    });

    // Charts
    const kdaData = [{{ implode(',', $aggregateStats['kdaPerMatch'] ?? []) }}];
    const csData = [{{ implode(',', $aggregateStats['csPerMinPerMatch'] ?? []) }}];
    const goldData = [{{ implode(',', $aggregateStats['goldPerMinPerMatch'] ?? []) }}];
    const gameLabels = Array.from({length: kdaData.length}, (_, i) => `Game ${i + 1}`);

    const lineChartOptions = {
        responsive: true, maintainAspectRatio: false,
        scales: { y: { beginAtZero: true, ticks: { color: '#9ca3af' } }, x: { ticks: { color: '#9ca3af' } } },
        plugins: { legend: { display: false } }
    };

    const kdaCtx = document.getElementById('kdaChart');
    if (kdaCtx) {
        new Chart(kdaCtx, {
            type: 'line',
            data: { labels: gameLabels, datasets: [{ label: 'KDA', data: kdaData, fill: true, backgroundColor: 'rgba(59, 130, 246, 0.2)', borderColor: 'rgba(59, 130, 246, 1)', tension: 0.3, pointBackgroundColor: 'rgba(59, 130, 246, 1)' }] },
            options: { ...lineChartOptions, plugins: { ...lineChartOptions.plugins, title: { display: true, text: 'KDA per Match', color: 'white' } } }
        });
    }

    const csCtx = document.getElementById('csChart');
    if (csCtx) {
        new Chart(csCtx, {
            type: 'line',
            data: { labels: gameLabels, datasets: [{ label: 'CS/min', data: csData, fill: true, backgroundColor: 'rgba(234, 179, 8, 0.2)', borderColor: 'rgba(234, 179, 8, 1)', tension: 0.3, pointBackgroundColor: 'rgba(234, 179, 8, 1)' }] },
            options: { ...lineChartOptions, plugins: { ...lineChartOptions.plugins, title: { display: true, text: 'CS/min per Match', color: 'white' } } }
        });
    }

    const goldCtx = document.getElementById('goldChart');
    if (goldCtx) {
        new Chart(goldCtx, {
            type: 'line',
            data: { labels: gameLabels, datasets: [{ label: 'Gold/min', data: goldData, fill: true, backgroundColor: 'rgba(34, 197, 94, 0.2)', borderColor: 'rgba(34, 197, 94, 1)', tension: 0.3, pointBackgroundColor: 'rgba(34, 197, 94, 1)' }] },
            options: { ...lineChartOptions, plugins: { ...lineChartOptions.plugins, title: { display: true, text: 'Gold/min per Match', color: 'white' } } }
        });
    }

    const winLossCtx = document.getElementById('winLossChart');
    if (winLossCtx) {
        new Chart(winLossCtx, {
            type: 'doughnut',
            data: {
                labels: ['Wins', 'Losses'],
                datasets: [{
                    data: [{{ $aggregateStats['wins'] }}, {{ $aggregateStats['losses'] }}],
                    backgroundColor: ['rgba(59, 130, 246, 0.8)', 'rgba(239, 68, 68, 0.8)'],
                    borderColor: ['rgba(30, 41, 59, 1)', 'rgba(30, 41, 59, 1)'],
                    borderWidth: 2
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '60%', plugins: { legend: { display: false } } }
        });
    }
});
</script>
@endpush
