@extends('layouts.app')

@section('title', 'Items')

@section('content')
    <h1 class="text-4xl font-bold mb-4">League of Legends Items</h1>

    <!-- Search and Filter -->
    <div class="bg-gray-800 rounded-lg p-4 mb-8 sticky top-4 z-20">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <div class="lg:col-span-1">
                <input type="text" id="item-search" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search by name...">
            </div>
            <div class="lg:col-span-3 flex flex-wrap items-center justify-start gap-2">
                <button class="filter-btn active-filter bg-blue-500 text-white font-bold px-3 py-1.5 rounded-lg text-xs" data-tag="All">All</button>
                @foreach($tags as $tag => $tagName)
                    <button class="filter-btn bg-gray-700 hover:bg-gray-600 text-white font-bold px-3 py-1.5 rounded-lg text-xs" data-tag="{{ $tag }}">{{ $tagName }}</button>
                @endforeach
            </div>
        </div>
    </div>

    @if(isset($error))
        <div class="bg-red-500 text-white font-bold rounded-lg p-4">
            {{ $error }}
        </div>
    @else
        <div id="item-sections-container" class="space-y-12">
            @foreach($groupedItems as $tag => $items)
                <section class="item-section" data-section-tag="{{ $tag }}">
                    <h2 class="text-3xl font-bold mb-4 border-b-2 border-gray-700 pb-2">{{ $tagTranslations[$tag] ?? $tag }}</h2>
                    <div class="grid grid-cols-5 sm:grid-cols-8 md:grid-cols-10 lg:grid-cols-12 xl:grid-cols-14 gap-4">
                        @foreach($items as $item)
                            <div class="item-card" data-name="{{ strtolower($item['name']) }}" data-tags="{{ strtolower(implode(',', $item['tags'])) }}">
                                <a href="/item/{{ $item['id'] }}" title="{{ $item['name'] }}">
                                    <div class="aspect-square bg-gray-900 rounded-lg flex items-center justify-center p-1 hover:bg-gray-700 transition">
                                        <img src="https://ddragon.leagueoflegends.com/cdn/{{ $version }}/img/item/{{ $item['id'] }}.png" alt="{{ $item['name'] }}" class="max-w-full max-h-full">
                                    </div>
                                    <div class="text-xs mt-1 text-center">{{ $item['name'] }}</div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('item-search');
    const filterButtons = document.querySelectorAll('.filter-btn');
    const itemSections = document.querySelectorAll('.item-section');

    let currentSearch = '';
    let currentTag = 'All';

    function filterAndGroupItems() {
        itemSections.forEach(section => {
            const sectionTag = section.dataset.sectionTag;
            const items = section.querySelectorAll('.item-card');
            let visibleItemsInSection = 0;

            // Determine if the section should be visible based on the tag filter
            const isSectionVisibleByTag = currentTag === 'All' || sectionTag.toLowerCase() === currentTag.toLowerCase();

            if (isSectionVisibleByTag) {
                // If section is visible by tag, then check items inside against the search term
                items.forEach(card => {
                    const nameMatch = card.dataset.name.includes(currentSearch);
                    if (nameMatch) {
                        card.classList.remove('hidden');
                        visibleItemsInSection++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                // Hide the entire section (including title) if no items match the search
                if (visibleItemsInSection > 0) {
                    section.classList.remove('hidden');
                } else {
                    section.classList.add('hidden');
                }
            } else {
                // If section is not visible by tag, hide the whole section
                section.classList.add('hidden');
            }
        });
    }

    searchInput.addEventListener('input', (e) => {
        currentSearch = e.target.value.toLowerCase();
        filterAndGroupItems();
    });

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            filterButtons.forEach(btn => {
                btn.classList.remove('active-filter', 'bg-blue-500');
                btn.classList.add('bg-gray-700', 'hover:bg-gray-600');
            });
            button.classList.add('active-filter', 'bg-blue-500');
            button.classList.remove('bg-gray-700', 'hover:bg-gray-600');

            currentTag = button.dataset.tag;
            filterAndGroupItems();
        });
    });
});
</script>
@endpush
