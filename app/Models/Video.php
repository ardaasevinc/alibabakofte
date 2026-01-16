<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'title', 'desc', 'video_url', 'video_file', 'order', 'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
    ];
}