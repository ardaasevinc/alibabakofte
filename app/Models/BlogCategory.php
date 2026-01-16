<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    protected $fillable = ['title', 'slug', 'desc', 'image', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    // Bloglar ile ilişki (Kategorinin birden fazla yazısı olabilir)
    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }
}