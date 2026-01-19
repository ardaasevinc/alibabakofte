<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\PhotoDelete\HasImageDeleting;

class Banner extends Model
{
    use HasImageDeleting;
    protected $fillable = ['title', 'desc', 'image', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

     protected array $imageFields = ['image'];
}