@extends('layouts.app')

@section('title', 'Champions')

@section('content')
    <h1 class="text-4xl font-bold mb-4">League of Legends Champions</h1>

    <!-- Search and Filter -->
    <div class="bg-gray-800 rounded-lg p-4 mb-8">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- Search Input -->
            <div class="flex-grow">
                <input type="text" id="champion-search" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search by name...">
            </div>
            <!-- Filter Buttons -->
            <div class="flex flex-wrap items-center gap-2">
                <button class="filter-btn active-filter bg-blue-500 text-white font-bold px-4 py-2 rounded-lg text-sm" data-tag="All">All</button>
                @foreach($tags as $tag)
                    <button class="filter-btn bg-gray-700 hover:bg-gray-600 text-white font-bold px-4 py-2 rounded-lg text-sm" data-tag="{{ $tag }}">{{ $tag }}</button>
                @endforeach
            </div>
        </div>
    </div>

    @if(isset($error))
        <div class="bg-red-500 text-white font-bold rounded-lg p-4">
            {{ $error }}
        </div>
    @else
        <div id="champion-grid" class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4">
            @foreach($champions as $champion)
                <a href="/champion/{{ $champion['id'] }}" 
                   class="champion-card bg-gray-800 rounded-lg p-4 flex flex-col items-center text-center hover:bg-gray-700 transition"
                   data-name="{{ strtolower($champion['name']) }}"
                   data-tags="{{ strtolower(implode(',', $champion->tags)) }}">
                    <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/champion/{{ $champion['id'] }}.png" alt="{{ $champion['name'] }}" class="w-24 h-24 rounded-full mb-2">
                    <span class="font-bold">{{ $champion['name'] }}</span>
                    <span class="text-gray-400 text-sm">{{ $champion['title'] }}</span>
                </a>
            @endforeach
        </div>
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('champion-search');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const championCards = document.querySelectorAll('.champion-card');

    let currentSearch = '';
    let currentTag = 'All';

    function filterChampions() {
        championCards.forEach(card => {
            const nameMatch = card.dataset.name.includes(currentSearch);
            const tagMatch = currentTag === 'All' || card.dataset.tags.includes(currentTag.toLowerCase());

            if (nameMatch && tagMatch) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    searchInput.addEventListener('input', (e) => {
        currentSearch = e.target.value.toLowerCase();
        filterChampions();
    });

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Update active button style
            filterButtons.forEach(btn => {
                btn.classList.remove('active-filter', 'bg-blue-500');
                btn.classList.add('bg-gray-700', 'hover:bg-gray-600');
            });
            button.classList.add('active-filter', 'bg-blue-500');
            button.classList.remove('bg-gray-700', 'hover:bg-gray-600');

            // Update tag and filter
            currentTag = button.dataset.tag;
            filterChampions();
        });
    });
});
</script>
@endpush