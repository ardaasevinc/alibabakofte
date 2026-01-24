<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'leads';

    /* ============================================================
     * Fillable
     * ============================================================ */
    protected $fillable = [
        // 1) Kullanıcı / Oturum
        'external_id',
        'session_id',

        // 2) Meta Event
        'event_id',
        'event_name',
        'type',
        'button_id',

        // 3) Trafik Kaynağı
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'fbclid',
        'gclid',

        // 4) Meta Browser IDs
        'fbp',
        'fbc',

        // 5) Kullanıcı Teknik Bilgisi
        'ip_address',
        'user_agent',
        'platform',
        'is_mobile',

        // 6) URL Bilgileri
        'came_from_url',
        'event_source_url',
        'landing_page',
        'referer',

        // 7) JSON alanları
        'payload',
        'meta_request',
        'meta_response',
    ];

    /* ============================================================
     * Casts
     * ============================================================ */
    protected $casts = [
        'payload'        => 'array',
        'meta_request'   => 'array',
        'meta_response'  => 'array',
        'is_mobile'      => 'boolean',
    ];

    /* ============================================================
     * Varsayılan değerler
     * ============================================================ */
    protected $attributes = [
        'event_name' => 'Lead',
    ];


    /* ============================================================
     * SCOPES — Filtreler
     * ============================================================ */

    // Bugün gelen kayıtlar
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // Belirli buton tipi: whatsapp / menu
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }

    // Reklamdan gelenler
    public function scopeAds($query)
    {
        return $query->whereNotNull('fbclid');
    }

    // UTM Bazlı filtre
    public function scopeUtm($query, string $key, string $value)
    {
        return $query->where("utm_{$key}", $value);
    }


    /* ============================================================
     * YARDIMCI METODLAR
     * ============================================================ */

    public function isMobileDevice(): bool
    {
        return (bool) $this->is_mobile;
    }

    public function devicePlatform(): string
    {
        return $this->platform ?? 'Unknown';
    }

    // Filament içinde küçük özet (istatistik kartları için ideal)
    public function summary(): string
    {
        return sprintf(
            '[%s] %s | %s → %s | IP: %s',
            strtoupper($this->type),
            $this->utm_source ?? 'direct',
            $this->utm_campaign ?? '-',
            $this->came_from_url ?? '-',
            $this->ip_address
        );
    }


    /* ============================================================
     * ACCESSORS — Filament için okunabilir JSON
     * ============================================================ */

    public function getFormattedRequestAttribute(): string
    {
        return json_encode($this->meta_request, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function getFormattedResponseAttribute(): string
    {
        return json_encode($this->meta_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
