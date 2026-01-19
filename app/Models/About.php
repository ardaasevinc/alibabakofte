<?php

namespace App\Models;

use App\PhotoDelete\HasImageDeleting;
use Illuminate\Database\Eloquent\Model;

class About extends Model
{

use HasImageDeleting;
    protected $fillable = ['title', 'desc', 'image', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

     protected array $imageFields = ['image'];
}