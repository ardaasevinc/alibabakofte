<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blog extends Model
{
    protected $fillable = [
        'blog_category_id', 'title', 'slug', 'desc', 'image', 'is_published', 'tags'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'tags' => 'array', // JSON alanı dizi olarak cast ediyoruz
    ];

    // Kategoriye ait olma ilişkisi
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }
}