<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\PhotoDelete\HasImageDeleting;

class BlogCategory extends Model
{

use HasImageDeleting;
    protected $fillable = ['title', 'slug', 'desc', 'image', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    // Bloglar ile ilişki (Kategorinin birden fazla yazısı olabilir)
    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

     protected array $imageFields = ['image'];
}