@extends('layouts.app')

@section('title', $item['name'] . ' - Item Details')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Item Info (Left Column) -->
        <div class="lg:col-span-2">
            <div class="bg-gray-800 rounded-lg p-8">
                <div class="flex items-start mb-4">
                    <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/item/{{ $itemId }}.png" alt="{{ $item['name'] }}" class="w-20 h-20 rounded-md mr-6">
                    <div>
                        <h1 class="text-4xl font-bold">{{ $item['name'] }}</h1>
                        <div class="text-yellow-400 font-bold text-lg">
                            {{ $item['gold']['total'] }}g 
                            <span class="text-gray-400 text-sm font-normal">(Venda: {{ $item['gold']['sell'] }}g)</span>
                        </div>
                        <!-- Tags -->
                        <div class="flex space-x-2 mt-2">
                            @foreach($item['tags'] as $tag)
                                <span class="bg-gray-700 text-gray-300 text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                @if(!empty($item['plaintext']))
                    <p class="text-gray-400 italic mb-6">"{{ $item['plaintext'] }}"</p>
                @endif

                <div class="text-gray-300 space-y-3">{!! $processedDescription !!}</div>
            </div>
        </div>

        <!-- Recipe and Builds Into (Right Column) -->
        <div class="space-y-8">
            <!-- Recipe -->
            @if(!empty($fromItems))
                <div class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Receita</h2>
                    <div class="flex items-center flex-wrap gap-2">
                        @foreach($fromItems as $fromId => $fromItem)
                            <div class="relative component-item">
                                <a href="/item/{{ $fromId }}" class="block">
                                    <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/item/{{ $fromId }}.png" alt="{{ $fromItem['name'] }}" class="w-12 h-12 rounded-md hover:scale-110 transition">
                                </a>
                                <div class="component-tooltip hidden absolute z-10 w-64 p-3 -top-4 left-16 bg-gray-950 rounded-lg shadow-lg border border-gray-700">
                                    <h4 class="font-bold text-white">{{ $fromItem['name'] }}</h4>
                                    <p class="text-xs text-yellow-400 mb-2">{{ $fromItem['gold']['total'] }}g</p>
                                    <p class="text-xs text-gray-400 italic">{{ $fromItem['plaintext'] }}</p>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <span class="text-gray-400 text-xl">+</span>
                            @endif
                        @endforeach
                        <span class="text-gray-400 text-xl">=</span>
                        <div class="text-center">
                             <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/item/{{ $itemId }}.png" alt="{{ $item['name'] }}" class="w-12 h-12 rounded-md mx-auto">
                        </div>
                    </div>
                    <div class="text-right text-sm mt-2 text-yellow-400">
                        Custo da Combinação: {{ $recipeCost }}g
                    </div>
                </div>
            @endif

            <!-- Builds Into -->
            @if(!empty($intoItems))
                <div class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-2xl font-bold mb-4">Builds Into</h2>
                    <div class="flex items-center flex-wrap gap-4">
                        @foreach($intoItems as $intoId => $intoItem)
                            <div class="relative component-item text-center">
                                <a href="/item/{{ $intoId }}" class="block">
                                    <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/item/{{ $intoId }}.png" alt="{{ $intoItem['name'] }}" class="w-12 h-12 rounded-md hover:scale-110 transition mx-auto">
                                    <span class="text-xs text-gray-400">{{ $intoItem['name'] }}</span>
                                </a>
                                <div class="component-tooltip hidden absolute z-10 w-64 p-3 -top-4 left-16 bg-gray-950 rounded-lg shadow-lg border border-gray-700 text-left">
                                    <h4 class="font-bold text-white">{{ $intoItem['name'] }}</h4>
                                    <p class="text-xs text-yellow-400 mb-2">{{ $intoItem['gold']['total'] }}g</p>
                                    <p class="text-xs text-gray-400 italic">{{ $intoItem['plaintext'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const componentItems = document.querySelectorAll('.component-item');

    componentItems.forEach(container => {
        const tooltip = container.querySelector('.component-tooltip');

        container.addEventListener('mouseenter', () => {
            tooltip.classList.remove('hidden');
            const rect = tooltip.getBoundingClientRect();
            if (rect.right > window.innerWidth) {
                tooltip.style.left = 'auto';
                tooltip.style.right = '100%';
            }
            if (rect.left < 0) {
                tooltip.style.left = '100%';
                tooltip.style.right = 'auto';
            }
        });

        container.addEventListener('mouseleave', () => {
            tooltip.classList.add('hidden');
            // Reset styles for next time
            tooltip.style.left = '100%';
            tooltip.style.right = 'auto';
        });
    });
});
</script>
@endpush
