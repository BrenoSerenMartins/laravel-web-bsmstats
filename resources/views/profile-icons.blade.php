@extends('layouts.app')

@section('title', 'Profile Icons')

@section('content')
    <h1 class="text-4xl font-bold mb-8">Summoner Profile Icons</h1>

    @if(isset($error))
        <div class="bg-red-500 text-white font-bold rounded-lg p-4">
            {{ $error }}
        </div>
    @else
        <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 lg:grid-cols-12 xl:grid-cols-16 gap-4">
            @foreach($icons as $icon)
                <div title="Icon ID: {{ $icon['id'] }}">
                    <div class="aspect-square bg-gray-800 rounded-lg flex items-center justify-center p-1 hover:bg-gray-700 transition">
                        <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/profileicon/{{ $icon['image']['full'] }}" alt="Profile Icon {{ $icon['id'] }}" class="max-w-full max-h-full rounded-md">
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination Links -->
        <div class="mt-8">
            {{ $icons->links() }}
        </div>
    @endif
@endsection