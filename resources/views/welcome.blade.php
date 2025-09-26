@extends('layouts.app')

@section('title', 'BSMStats')

@section('content')
<div class="bg-gray-800 rounded-lg p-8">
    <h1 class="text-4xl font-bold mb-4">Search for a Summoner</h1>
    <form action="/summoner" method="GET" class="flex items-center">
        <div class="flex w-full bg-gray-700 rounded-l-lg">
            <input type="text" name="gameName" class="w-3/4 bg-transparent text-white px-4 py-2 focus:outline-none"
                placeholder="Game Name">
            <span class="text-gray-500 text-2xl self-center">#</span>
            <input type="text" name="tagLine" class="w-1/4 bg-transparent text-white px-4 py-2 focus:outline-none"
                placeholder="TAG">
        </div>
        <button type="submit"
            class="bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-r-lg px-6 py-2">Search</button>
    </form>
</div>
@endsection