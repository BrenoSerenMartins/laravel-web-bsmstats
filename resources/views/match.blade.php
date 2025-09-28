@extends('layouts.app')

@section('title', 'EzStats - Match Details')

@section('content')
    <div class="bg-gray-800 rounded-lg p-8">
        @if (isset($error))
            <h1 class="text-4xl font-bold mb-4">Error</h1>
            <p class="text-red-500">{{ $error }}</p>
        @else
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-4xl font-bold">Match Details</h1>
                    <p class="text-gray-400">{{ $matchData['info']['gameMode'] }}</p>
                </div>
                <p class="text-gray-400">{{ date('Y-m-d H:i:s', $matchData['info']['gameCreation'] / 1000) }}</p>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div>
                    <h2 class="text-2xl font-bold mb-4">Blue Team</h2>
                    @foreach ($matchData['info']['participants'] as $participant)
                        @if ($participant['teamId'] == 100)
                            <div class="flex items-center mb-4">
                                <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/champion/{{ $participant['championName'] }}.png" alt="{{ $participant['championName'] }}" class="w-12 h-12 rounded-full mr-4">
                                <div>
                                    <p class="font-bold">{{ $participant['summonerName'] }}</p>
                                    <p class="text-gray-400">{{ $participant['kills'] }}/{{ $participant['deaths'] }}/{{ $participant['assists'] }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div>
                    <h2 class="text-2xl font-bold mb-4">Red Team</h2>
                    @foreach ($matchData['info']['participants'] as $participant)
                        @if ($participant['teamId'] == 200)
                            <div class="flex items-center mb-4">
                                <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/champion/{{ $participant['championName'] }}.png" alt="{{ $participant['championName'] }}" class="w-12 h-12 rounded-full mr-4">
                                <div>
                                    <p class="font-bold">{{ $participant['summonerName'] }}</p>
                                    <p class="text-gray-400">{{ $participant['kills'] }}/{{ $participant['deaths'] }}/{{ $participant['assists'] }}</p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
