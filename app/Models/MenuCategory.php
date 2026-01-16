<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    protected $fillable = ['title', 'desc', 'image', 'order', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
    ];

    // Menü öğeleri ile ilişki (Birazdan oluşturacağız)
    public function menuItems()
    {
        return $this->hasMany(MenuItem::class);
    }
}