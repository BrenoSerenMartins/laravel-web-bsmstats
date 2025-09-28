<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Champion extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'key',
        'name',
        'title',
        'blurb',
        'info',
        'image',
        'tags',
        'partype',
        'stats',
        'spells',
        'passive',
        'lore',
        'allytips',
        'enemytips',
    ];

    protected $casts = [
        'info' => 'array',
        'image' => 'array',
        'tags' => 'array',
        'stats' => 'array',
        'spells' => 'array',
        'passive' => 'array',
        'allytips' => 'array',
        'enemytips' => 'array',
    ];
}