<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\PhotoDelete\HasImageDeleting;
class Video extends Model
{

use HasImageDeleting;
    protected $fillable = [
        'title', 'desc', 'video_url', 'video_file', 'order', 'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
    ];

     protected array $imageFields = ['video_file'];
}