<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'leads';

    protected $fillable = [
        'external_id', 'session_id', 'event_id', 'event_name', 'type', 'button_id',
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
        'fbclid', 'gclid', 'fbp', 'fbc', 'ip_address', 'user_agent', 'platform',
        'is_mobile', 'came_from_url', 'event_source_url', 'landing_page', 'referer',
        'payload', 'meta_request', 'meta_response',
    ];

    protected $casts = [
        'payload'        => 'array',
        'meta_request'   => 'array',
        'meta_response'  => 'array',
        'is_mobile'      => 'boolean',
    ];

    protected $attributes = [
        'event_name' => 'Lead',
    ];

    /* ============================================================
     * SCOPES
     * ============================================================ */
    public function scopeToday(Builder $query): void { $query->whereDate('created_at', today()); }
    public function scopeAds(Builder $query): void { $query->whereNotNull('fbclid'); }

    /* ============================================================
     * ACCESSORS (Hatayı Çözen Kısım)
     * ============================================================ */

    public function getFormattedRequestAttribute(): ?string
    {
        if (empty($this->meta_request)) return null;
        return json_encode($this->meta_request, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function getFormattedResponseAttribute(): ?string
    {
        if (empty($this->meta_response)) return null;
        return json_encode($this->meta_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    public function getUserAgentShortAttribute(): string
{
    return str($this->user_agent)->limit(50);
}
}