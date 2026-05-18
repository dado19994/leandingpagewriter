<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'quote',
        'author',
        'context',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
