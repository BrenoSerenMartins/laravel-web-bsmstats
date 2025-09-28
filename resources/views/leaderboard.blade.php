@extends('layouts.app')

@section('title', 'EzStats - Leaderboard')

@section('content')
    <div class="bg-gray-800 rounded-lg p-8">
        @if (isset($error))
            <h1 class="text-4xl font-bold mb-4">Error</h1>
            <p class="text-red-500">{{ $error }}</p>
        @else
            <h1 class="text-4xl font-bold mb-4">Challenger Leaderboard</h1>
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left py-2">Rank</th>
                        <th class="text-left py-2">Summoner</th>
                        <th class="text-left py-2">LP</th>
                        <th class="text-left py-2">Wins</th>
                        <th class="text-left py-2">Losses</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($leaderboardData['entries'] as $entry)
                        <tr class="border-b border-gray-700">
                            <td class="py-2">{{ $loop->iteration }}</td>
                            <td class="py-2"><a href="/summoner?name={{ $entry['summonerName'] }}" class="text-blue-400 hover:text-blue-500">{{ $entry['summonerName'] }}</a></td>
                            <td class="py-2">{{ $entry['leaguePoints'] }}</td>
                            <td class="py-2">{{ $entry['wins'] }}</td>
                            <td class="py-2">{{ $entry['losses'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection