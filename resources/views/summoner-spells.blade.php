@extends('layouts.app')

@section('title', 'Summoner Spells')

@section('content')
    <h1 class="text-4xl font-bold mb-4">Summoner Spells</h1>

    <!-- Search and Filter -->
    <div class="bg-gray-800 rounded-lg p-4 mb-8 sticky top-4 z-20">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div class="lg:col-span-1">
                <input type="text" id="spell-search" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search by name...">
            </div>
            <div class="lg:col-span-3 flex flex-wrap items-center justify-start gap-2">
                <button class="filter-btn active-filter bg-blue-500 text-white font-bold px-3 py-1.5 rounded-lg text-xs" data-mode="All">All</button>
                @foreach($modes as $modeKey => $modeName)
                    <button class="filter-btn bg-gray-700 hover:bg-gray-600 text-white font-bold px-3 py-1.5 rounded-lg text-xs" data-mode="{{ $modeKey }}">{{ $modeName }}</button>
                @endforeach
            </div>
        </div>
    </div>

    @if(isset($error))
        <div class="bg-red-500 text-white font-bold rounded-lg p-4">
            {{ $error }}
        </div>
    @else
        <div id="spells-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($spells as $spell)
                <div class="spell-card bg-gray-800 rounded-lg p-6 flex items-start" 
                     data-modes="{{ strtolower(implode(',', $spell['modes'])) }}"
                     data-name="{{ strtolower($spell['name']) }}">
                    <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/spell/{{ $spell['id'] }}.png" alt="{{ $spell['name'] }}" class="w-20 h-20 rounded-md mr-6">
                    <div>
                        <h2 class="text-2xl font-bold text-yellow-400">{{ $spell['name'] }}</h2>
                        <p class="text-sm text-gray-400 mb-2">Cooldown: {{ $spell['cooldownBurn'] }}s</p>
                        <p class="text-gray-300">{{ $spell['description'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('spell-search');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const spellCards = document.querySelectorAll('.spell-card');

    let currentSearch = '';
    let currentMode = 'All';

    function filterSpells() {
        spellCards.forEach(card => {
            const nameMatch = card.dataset.name.includes(currentSearch);
            const modeMatch = currentMode === 'All' || card.dataset.modes.includes(currentMode.toLowerCase());

            if (nameMatch && modeMatch) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    searchInput.addEventListener('input', (e) => {
        currentSearch = e.target.value.toLowerCase();
        filterSpells();
    });

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            filterButtons.forEach(btn => {
                btn.classList.remove('active-filter', 'bg-blue-500');
                btn.classList.add('bg-gray-700', 'hover:bg-gray-600');
            });
            button.classList.add('active-filter', 'bg-blue-500');
            button.classList.remove('bg-gray-700', 'hover:bg-gray-600');

            currentMode = button.dataset.mode;
            filterSpells();
        });
    });
});
</script>
@endpush
