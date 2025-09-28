<?php

use App\Http\Controllers\SummonerController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\ChampionController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SummonerSpellController;
use App\Http\Controllers\ProfileIconController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/summoner', [SummonerController::class, 'search'])->name('summoner.search');
Route::get('/match/{matchId}', [MatchController::class, 'show']);
Route::get('/leaderboard', [LeaderboardController::class, 'index']);
Route::get('/champions', [ChampionController::class, 'index'])->name('champions.index');
Route::get('/champion/{championId}', [ChampionController::class, 'show']);
Route::get('/items', [ItemController::class, 'index']);
Route::get('/item/{itemId}', [ItemController::class, 'show']);
Route::get('/sync-champions', [ChampionController::class, 'sync'])->name('champions.sync');
Route::get('/summoner-spells', [SummonerSpellController::class, 'index']);
Route::get('/profile-icons', [ProfileIconController::class, 'index']);
