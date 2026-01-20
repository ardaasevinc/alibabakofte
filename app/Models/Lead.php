<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'type', 'event_id', 'event_name',
        'utm_source', 'utm_campaign', 'utm_medium',
        'fbclid', 'gclid',
        'device_id', 'session_hash', 'fbp', 'fbc', 'browser_id',
        'ip_address', 'user_agent', 'referer', 'landing_page',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
