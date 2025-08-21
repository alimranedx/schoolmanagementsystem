<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',      // Y-m-d
        'name',
        'type',      // govt | school
        'country',   // ISO country name or code
        'year',
    ];

    protected $casts = [
        'date' => 'date:Y-m-d',
        'year' => 'integer',
    ];
}
