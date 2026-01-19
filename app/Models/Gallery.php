<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\PhotoDelete\HasImageDeleting;

class Gallery extends Model
{
    use HasImageDeleting;
    protected $fillable = ['title', 'desc', 'image', 'order', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
    ];

     protected array $imageFields = ['image'];
}
