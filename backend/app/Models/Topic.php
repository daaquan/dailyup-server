<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'url', 'category', 'published_at'];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
