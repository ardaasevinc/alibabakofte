<?php


namespace App\Models;

use App\PhotoDelete\HasImageDeleting;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{

    use HasImageDeleting;
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

     protected array $imageFields = ['image'];
}