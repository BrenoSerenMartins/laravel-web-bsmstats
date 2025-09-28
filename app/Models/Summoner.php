<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Summoner extends Model
{
    protected $primaryKey = 'puuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'puuid',
        'gameName',
        'tagLine',
        'summonerId',
        'accountId',
        'profileIconId',
        'summonerLevel',
        'revisionDate',
    ];
}