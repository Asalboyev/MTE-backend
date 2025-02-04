<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;
// eskini yangilashi uchun uni topish kerak keyin yangilash kerak model qanday tuzilgan 
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'message',
        'type',
        'page',
        'company'
    ];
}
