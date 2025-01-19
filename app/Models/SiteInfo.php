<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'logo',
        'logo_dark',
        'desc',
        'address',
        'phone_number',
        'email',
        'work_time',
        'map',
        'exchange',
        'favicon',
        'telegram',
        'instagram',
        'facebook',
        'youtube',
    ];

    protected $casts = [
        'title' => 'array',
        'desc' => 'array',
        'address' => 'array',
        'work_time' => 'array',
    ];
}
