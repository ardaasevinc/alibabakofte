<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    /**
     * Toplu atama yapılabilecek alanlar.
     */
    protected $fillable = [
        'type',
        'event_id',
        'event_name',
        'utm_source',
        'utm_campaign',
        'utm_medium',
        'fbclid',
        'ip_address',
        'user_agent',
        'payload',
    ];

    /**
     * Veritabanından çekilirken otomatik dönüştürülecek alanlar.
     */
    protected $casts = [
        'payload' => 'json', // JSON veriyi otomatik diziye çevirir
    ];
}