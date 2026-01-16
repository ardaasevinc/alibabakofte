<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstagramPost extends Model
{
   protected $fillable = [
    'instagram_id',
    'media_type',
    'media_url',
    'permalink',
    'caption',
    'is_published',
    'posted_at',
];

protected $casts = [
    'posted_at' => 'datetime',
    'is_published' => 'boolean',
];
}
