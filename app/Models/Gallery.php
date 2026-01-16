<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['title', 'desc', 'image', 'order', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
    ];
}
