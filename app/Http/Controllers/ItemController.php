<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\FetchesDataDragonVersion;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ItemController extends Controller
{
    use FetchesDataDragonVersion;

    public function index()
    {
        $latestVersion = $this->getLatestDataDragonVersion();
        $items = $this->getAllItems($latestVersion);

        if (!$items) {
            return view('items', ['error' => 'Could not retrieve item data.']);
        }

        // Filter for items that are in the Summoner's Rift store
        $filteredItems = collect($items)->filter(function ($item) {
            return isset($item['maps']['11']) && $item['maps']['11'] === true && !isset($item['requiredAlly']);
        });

        // Add the item ID into the item array itself so it's not lost after grouping
        $itemsWithIds = $filteredItems->map(function ($item, $itemId) {
            $item['id'] = $itemId;
            return $item;
        });

        // Tag name mapping
        $tagTranslations = [
            'Health' => 'Vida',
            'SpellDamage' => 'Dano Mágico',
            'AttackSpeed' => 'Velocidade de Ataque',
            'CriticalStrike' => 'Acerto Crítico',
            'Armor' => 'Armadura',
            'SpellBlock' => 'Resist. Mágica',
            'Damage' => 'Dano de Ataque',
            'Lane' => 'Fase de Rotas',
            'Mana' => 'Mana & Regeneração',
            'LifeSteal' => 'Roubo de Vida',
            'OnHit' => 'Efeito ao Contato',
            'CooldownReduction' => 'Aceleração de Habilidade',
            'AbilityHaste' => 'Aceleração de Habilidade',
            'Boots' => 'Botas',
            'Consumable' => 'Consumíveis',
            'GoldPer' => 'Geração de Ouro',
            'Jungle' => 'Selva',
            'MagicPenetration' => 'Penetração Mágica',
            'ArmorPenetration' => 'Penetração de Armadura',
            'Aura' => 'Aura',
            'MagicResist' => 'Resist. Mágica',
            'NonbootsMovement' => 'Velocidade de Movimento',
            'SpellVamp' => 'Vampirismo Mágico',
            'Tenacity' => 'Tenacidade',
            'Trinket' => 'Amuletos',
            'Active' => 'Habilidade Ativa',
            'Stealth' => 'Furtividade',
            'Slow' => 'Lentidão',
            'Vision' => 'Visão',
            'HealthRegen' => 'Regeneração de Vida',
            'ManaRegen' => 'Regeneração de Mana',
        ];

        // Define the desired category order
        $categoryOrder = [
            'Boots', 'Jungle', 'Lane', // Starting & Early Game
            'Damage', 'CriticalStrike', 'AttackSpeed', 'OnHit', 'ArmorPenetration', 'LifeSteal', // Physical Damage
            'SpellDamage', 'Mana', 'MagicPenetration', 'SpellVamp', // Magical Damage
            'Health', 'Armor', 'SpellBlock', 'Tenacity', 'HealthRegen', // Defense
            'AbilityHaste', 'NonbootsMovement', // Utility
            'Consumable', 'Trinket', 'Vision', // Consumables & Vision
        ];

        // Group items by their first tag and sort them
        $groupedItems = $itemsWithIds->groupBy(function ($item) {
            return $item['tags'][0] ?? 'Outros';
        })->sortBy(function ($items, $tag) use ($categoryOrder) {
            $index = array_search($tag, $categoryOrder);
            return $index === false ? 999 : $index; // Place unordered items at the end
        });

        // Get all unique tags and translate them for the filter buttons
        $tags = $filteredItems->flatMap(function ($item) {
            return $item['tags'];
        })->unique()->sort()->values()->mapWithKeys(function ($tag) use ($tagTranslations) {
            return [$tag => $tagTranslations[$tag] ?? $tag];
        });

        return view('items', [
            'groupedItems' => $groupedItems,
            'tagTranslations' => $tagTranslations,
            'tags' => $tags,
            'version' => $latestVersion,
        ]);
    }

    public function show($itemId)
    {
        $latestVersion = $this->getLatestDataDragonVersion();
        $allItems = $this->getAllItems($latestVersion);

        if (!$allItems) {
            abort(404, 'Item data not found.');
        }

        // Find the requested item
        if (!isset($allItems[$itemId])) {
            abort(404, 'Item not found.');
        }
        $itemData = $allItems[$itemId];

        // --- START: Description Processing & Cost Calculation ---
        
        // Calculate recipe cost
        $recipeCost = $itemData['gold']['base'];

        // Process description for styling
        $processedDescription = $itemData['description'];
        $processedDescription = preg_replace('/<stats>(.*?)<\/stats>/', '<div class="text-green-400">$1</div>', $processedDescription);
        $processedDescription = preg_replace('/<passive>(.*?)<\/passive>/', '<div class="text-yellow-400 font-semibold mt-2">$1</div>', $processedDescription);
        $processedDescription = preg_replace('/<active>(.*?)<\/active>/', '<div class="text-orange-400 font-semibold mt-2">$1</div>', $processedDescription);
        $processedDescription = preg_replace('/<br>/i', '<br />', $processedDescription);

        // --- END: Processing ---

        // Find component items
        $fromItems = [];
        if (isset($itemData['from'])) {
            foreach ($itemData['from'] as $fromId) {
                if (isset($allItems[$fromId])) {
                    $fromItems[$fromId] = $allItems[$fromId];
                }
            }
        }

        // Find items it builds into
        $intoItems = [];
        if (isset($itemData['into'])) {
            foreach ($itemData['into'] as $intoId) {
                if (isset($allItems[$intoId])) {
                    $intoItems[$intoId] = $allItems[$intoId];
                }
            }
        }

        return view('item_detail', [
            'item' => $itemData,
            'itemId' => $itemId,
            'fromItems' => $fromItems,
            'intoItems' => $intoItems,
            'version' => $latestVersion,
            'recipeCost' => $recipeCost,
            'processedDescription' => $processedDescription,
        ]);
    }
}