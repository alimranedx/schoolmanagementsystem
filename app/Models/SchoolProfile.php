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
        'banner_image_path',
        'academic_year',
        'holidays',
        'address',
        'phone',
        'email',
        'website',
        'about',
        'established_year',
        'country',
        'district',
        'upazila',
        'timezone',
    ];

    protected $casts = [
        'holidays' => 'array',
    ];
}
