<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo_path',
        'academic_year',
        'holidays',
    ];

    protected $casts = [
        'holidays' => 'array',
    ];
}
