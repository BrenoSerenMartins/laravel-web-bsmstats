<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameMatch extends Model
{
    protected $table = 'matches';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'puuid',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}