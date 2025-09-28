@extends('layouts.app')

@section('title', $champion['name'] . ' - Champion Details')

@section('content')
    <!-- Champion Header -->
    <div class="bg-gray-800 rounded-lg p-8 mb-8 flex items-start">
        <!-- Champion Portrait -->
        <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/champion/{{ $champion['id'] }}.png" alt="{{ $champion['name'] }}" class="w-32 h-32 rounded-lg mr-8">
        
        <!-- Info and Lore -->
        <div>
            <h1 class="text-5xl font-bold">{{ $champion['name'] }}</h1>
            <p class="text-xl text-gray-300 mb-2">{{ $champion['title'] }}</p>
            <!-- Tags -->
            <div class="flex space-x-2 mb-4">
                @foreach($champion['tags'] as $tag)
                    <span class="bg-gray-700 text-gray-300 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $tag }}</span>
                @endforeach
            </div>
            <h2 class="text-2xl font-bold mb-2">Lore</h2>
            <p class="text-gray-400 leading-relaxed">{{ $champion['lore'] }}</p>
        </div>
    </div>

    <!-- Grid for Info and Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Info Bars -->
        <div class="bg-gray-800 rounded-lg p-8 lg:col-span-1">
            <h2 class="text-3xl font-bold mb-6">Info</h2>
            <div class="space-y-4">
                <!-- Attack -->
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-base font-medium text-gray-300 flex items-center">
                            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.47 2.23a1 1 0 0 1 1.2 1.5l-3.07 4.34 4.34-3.07a1 1 0 0 1 1.5 1.2l-7.24 10.13a1 1 0 0 1-1.6-.2L2.23 3.73a1 1 0 0 1 1.2-1.5l12.04.0zM5.5 21.5a2 2 0 1 0 0-4 2 2 0 0 0 0 4z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Attack
                        </span>
                        <span class="text-sm font-medium text-gray-300">{{ $champion['info']['attack'] }} / 10</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2.5">
                        <div class="bg-red-500 h-2.5 rounded-full" style="width: {{ $champion['info']['attack'] * 10 }}%"></div>
                    </div>
                </div>
                <!-- Defense -->
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-base font-medium text-gray-300 flex items-center">
                            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Defense
                        </span>
                        <span class="text-sm font-medium text-gray-300">{{ $champion['info']['defense'] }} / 10</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2.5">
                        <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ $champion['info']['defense'] * 10 }}%"></div>
                    </div>
                </div>
                <!-- Magic -->
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-base font-medium text-gray-300 flex items-center">
                            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 13l4-4 4 4-4 4-4-4zM13 3l4 4-4 4-4-4 4-4zM8 18l4 4 4-4-4-4-4 4z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Magic
                        </span>
                        <span class="text-sm font-medium text-gray-300">{{ $champion['info']['magic'] }} / 10</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2.5">
                        <div class="bg-purple-500 h-2.5 rounded-full" style="width: {{ $champion['info']['magic'] * 10 }}%"></div>
                    </div>
                </div>
                <!-- Difficulty -->
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-base font-medium text-gray-300 flex items-center">
                            <svg class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm-2 14a2 2 0 1 1 4 0 2 2 0 0 1-4 0zm2-10a2.5 2.5 0 0 1 2.5 2.5c0 1.2-.8 2.3-2 2.4V12h-1v-.1c-1.2-.1-2-1.2-2-2.4A2.5 2.5 0 0 1 12 6z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            Difficulty
                        </span>
                        <span class="text-sm font-medium text-gray-300">{{ $champion['info']['difficulty'] }} / 10</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2.5">
                        <div class="bg-yellow-500 h-2.5 rounded-full" style="width: {{ $champion['info']['difficulty'] * 10 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Base Stats -->
        <div class="bg-gray-800 rounded-lg p-8 lg:col-span-2">
            <h2 class="text-3xl font-bold mb-6">Base Stats</h2>
            @php
                $statNames = [
                    'hp' => 'HP',
                    'hpperlevel' => 'HP per Level',
                    'mp' => 'Mana',
                    'mpperlevel' => 'Mana per Level',
                    'movespeed' => 'Move Speed',
                    'armor' => 'Armor',
                    'armorperlevel' => 'Armor per Level',
                    'spellblock' => 'Magic Resist',
                    'spellblockperlevel' => 'Magic Resist per Level',
                    'attackrange' => 'Attack Range',
                    'hpregen' => 'HP Regen',
                    'hpregenperlevel' => 'HP Regen per Level',
                    'mpregen' => 'Mana Regen',
                    'mpregenperlevel' => 'Mana Regen per Level',
                    'crit' => 'Crit Chance',
                    'critperlevel' => 'Crit per Level',
                    'attackdamage' => 'Attack Damage',
                    'attackdamageperlevel' => 'AD per Level',
                    'attackspeedperlevel' => 'AS per Level',
                    'attackspeed' => 'Attack Speed',
                ];
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                <div class="bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-sm text-gray-400">Resource</div>
                    <div class="text-xl font-bold text-white">{{ $champion['partype'] }}</div>
                </div>
                @foreach($champion['stats'] as $statName => $statValue)
                    <div class="bg-gray-700 rounded-lg p-3 text-center">
                        <div class="text-sm text-gray-400">{{ $statNames[$statName] ?? ucfirst(preg_replace('/(?<!^)[A-Z]/', ' $0', $statName)) }}</div>
                        <div class="text-xl font-bold text-white">{{ $statValue }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Spells -->
    <div class="bg-gray-800 rounded-lg p-8 mb-8">
        <h2 class="text-3xl font-bold mb-4">Abilities</h2>
        <div class="flex space-x-4">
            @php
                $championKey = str_pad($champion['key'], 4, '0', STR_PAD_LEFT);
            @endphp
            <!-- Passive -->
            <div class="relative ability-container">
                <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/passive/{{ $champion['passive']['image']['full'] }}" alt="{{ $champion['passive']['name'] }}" class="w-16 h-16 rounded-lg ability-icon cursor-pointer">
                <!-- Video Tooltip -->
                <div class="video-tooltip hidden absolute z-10 w-80 p-0 -top-4 left-24 bg-black rounded-lg shadow-lg border border-gray-700 overflow-hidden">
                    <video class="w-full" autoplay muted loop playsinline>
                        <source src="https://d28xe8vt774jo5.cloudfront.net/champion-abilities/{{ $championKey }}/ability_{{ $championKey }}_P1.mp4" type="video/mp4">
                    </video>
                    <div class="p-3">
                        <h4 class="font-bold text-lg mb-2">{{ $champion['passive']['name'] }} (Passive)</h4>
                        <p class="text-sm text-gray-300">{!! $champion['passive']['description'] !!}</p>
                    </div>
                </div>
            </div>
            <!-- Active Spells -->
            @foreach($champion['spells'] as $index => $spell)
                @php
                    $spellKey = ['Q', 'W', 'E', 'R'][$index];
                @endphp
                <div class="relative ability-container">
                    <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/spell/{{ $spell['image']['full'] }}" alt="{{ $spell['name'] }}" class="w-16 h-16 rounded-lg ability-icon cursor-pointer">
                    <!-- Video Tooltip -->
                    <div class="video-tooltip hidden absolute z-10 w-80 p-0 -top-8 left-20 bg-black rounded-lg shadow-lg border border-gray-700 overflow-hidden">
                        <video class="w-full" autoplay muted loop playsinline>
                            <source src="https://d28xe8vt774jo5.cloudfront.net/champion-abilities/{{ $championKey }}/ability_{{ $championKey }}_{{ $spellKey }}1.mp4" type="video/mp4">
                        </video>
                        <div class="p-3">
                            <h4 class="font-bold text-lg mb-2">{{ $spell['name'] }}</h4>
                            <div class="text-sm text-gray-300 space-y-2 border-b border-gray-600 pb-2">
                                {!! $spell['processed_tooltip'] !!}
                            </div>
                            <div class="text-xs text-gray-400 mt-2 space-y-1">
                                @if(!empty($spell['costBurn']) && $spell['costBurn'] != '0')
                                    <p><strong>Cost:</strong> {{ $spell['costBurn'] }} {{ $champion['partype'] }}</p>
                                @endif
                                @if(!empty($spell['cooldownBurn']) && $spell['cooldownBurn'] != '0')
                                    <p><strong>Cooldown:</strong> {{ $spell['cooldownBurn'] }}s</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Tips -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Ally Tips -->
        <div class="bg-gray-800 rounded-lg p-8">
            <h2 class="text-3xl font-bold mb-4 text-green-400">Playing As {{ $champion['name'] }}</h2>
            <ul class="space-y-3">
                @foreach($champion['allytips'] as $tip)
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-3 text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        <span class="text-gray-300">{{ $tip }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        <!-- Enemy Tips -->
        <div class="bg-gray-800 rounded-lg p-8">
            <h2 class="text-3xl font-bold mb-4 text-red-400">Playing Against {{ $champion['name'] }}</h2>
            <ul class="space-y-3">
                @foreach($champion['enemytips'] as $tip)
                    <li class="flex items-start">
                        <svg class="w-5 h-5 mr-3 text-red-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 12H6"></path></svg>
                        <span class="text-gray-300">{{ $tip }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Skins -->
    <div class="bg-gray-800 rounded-lg p-8">
        <h2 class="text-3xl font-bold mb-4">Skins</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($champion['skins'] as $skin)
                @if($skin['num'] != 0)
                    <div>
                        <img src="https://ddragon.leagueoflegends.com/cdn/img/champion/loading/{{ $champion['id'] }}_{{ $skin['num'] }}.jpg" alt="{{ $skin['name'] }}" class="w-full h-auto rounded-lg mb-2">
                        <p class="font-bold text-center text-sm">{{ $skin['name'] }}</p>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const abilityContainers = document.querySelectorAll('.ability-container');

    abilityContainers.forEach(container => {
        const icon = container.querySelector('.ability-icon');
        const tooltip = container.querySelector('.video-tooltip');

        if (icon && tooltip) {
            icon.addEventListener('mouseenter', () => {
                // Reset position
                tooltip.style.left = '80px'; // Default left position (20 * 4px)
                tooltip.style.right = 'auto';
                tooltip.style.top = '-32px'; // Default top position (-8 * 4px)
                tooltip.style.bottom = 'auto';

                tooltip.classList.remove('hidden');

                // Check viewport boundaries
                const rect = tooltip.getBoundingClientRect();
                if (rect.right > window.innerWidth) {
                    tooltip.style.left = 'auto';
                    tooltip.style.right = '80px';
                }
                if (rect.bottom > window.innerHeight) {
                    tooltip.style.top = 'auto';
                    tooltip.style.bottom = '0px';
                }
                if (rect.left < 0) {
                    tooltip.style.left = '80px';
                    tooltip.style.right = 'auto';
                }
                if (rect.top < 0) {
                    tooltip.style.top = '0px';
                    tooltip.style.bottom = 'auto';
                }

                const video = tooltip.querySelector('video');
                if (video) {
                    video.play();
                }
            });

            icon.addEventListener('mouseleave', () => {
                tooltip.classList.add('hidden');
                const video = tooltip.querySelector('video');
                if (video) {
                    video.pause(); // Pause video on mouse leave
                }
            });
        }
    });
});
</script>
@endpush