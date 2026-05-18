<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'status',
        'genre',
        'description',
        'synopsis',
        'excerpt',
        'reviews',
        'cover',
        'amazon_url',
        'meta_title',
        'meta_description',
        'is_featured',
    ];

    protected $casts = [
        'reviews' => 'array',
        'is_featured' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
