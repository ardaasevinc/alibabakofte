<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'leads';

    /**
     * Mass Assignment (DB kolonlarının tamamı)
     */
    protected $fillable = [
        'type',
        'event_id',
        'event_name',

        'utm_source',
        'utm_campaign',
        'utm_medium',
        'utm_term',
        'utm_content',

        'fbclid',
        'fbc',
        'fbp',

        'came_from_url',

        'ip_address',
        'user_agent',

        'platform',
        'is_mobile',

        'payload',
    ];

    /**
     * Cast'ler
     */
    protected $casts = [
        'payload'   => 'array',
        'is_mobile' => 'boolean',
    ];

    /**
     * Varsayılan değerler
     */
    protected $attributes = [
        'event_name' => 'Lead',
    ];

    /**
     * Scope: UTM filtreleme
     */
    public function scopeUtm($query, $key, $value)
    {
        return $query->where("utm_{$key}", $value);
    }

    /**
     * Scope: Belirli buton tipleri
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope: Bugün gelen Lead'ler
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope: Reklam Lead'leri (utm_medium = reklam)
     */
    public function scopeAds($query)
    {
        return $query->where('utm_medium', 'reklam');
    }

    /**
     * Kullanıcı mobil mi?
     */
    public function isMobileDevice(): bool
    {
        return (bool) $this->is_mobile;
    }

    /**
     * Platform bilgisi: iOS / Android / Desktop
     */
    public function devicePlatform(): ?string
    {
        return $this->platform;
    }

    /**
     * Lead kaynağı
     */
    public function source(): ?string
    {
        return $this->utm_source ?? 'direct';
    }

    /**
     * Lead’in geldiği tüm verileri tek satır özet olarak döndürür
     */
    public function summary(): string
    {
        return sprintf(
            '[%s] %s | %s → %s | IP: %s',
            strtoupper($this->type),
            $this->source(),
            $this->utm_campaign ?? '-',
            $this->came_from_url ?? '-',
            $this->ip_address
        );
    }
}
