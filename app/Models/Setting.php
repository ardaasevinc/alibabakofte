<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\{File, Log, Cache};

class Setting extends Model
{
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::saved(function ($setting) {
            // .env mapping: Veritabanı sütun isimleri soldaki ENV anahtarlarına atanır
            $envMapping = [
                'APP_URL'               => $setting->app_url,
                'APP_ENV'               => $setting->app_env,
                'APP_DEBUG'             => $setting->app_debug ? 'true' : 'false',
                'MAIL_HOST'             => $setting->mail_host,
                'MAIL_PORT'             => $setting->mail_port,
                'MAIL_USERNAME'         => $setting->mail_username,
                'MAIL_PASSWORD'         => $setting->mail_password,
                'MAIL_FROM_ADDRESS'     => $setting->mail_from_address,
                'MAIL_FROM_NAME'        => $setting->mail_from_name,
                // Resource'daki TextInput(facebook_pixel_code) -> .env(FACEBOOK_PIXEL_ID)
                'FACEBOOK_PIXEL_ID'     => $setting->facebook_pixel_code,
                'FACEBOOK_ACCESS_TOKEN' => $setting->facebook_access_token,
                'GOOGLE_ANALYTICS_ID'   => $setting->google_analytics_code,
            ];

            try {
                $envPath = base_path('.env');
                if (File::exists($envPath)) {
                    $content = File::get($envPath);

                    foreach ($envMapping as $key => $value) {
                        $value = $value ?? '';
                        // Çift tırnak ve ters eğik çizgi kaçırma işlemi (addslashes güvenlidir)
                        $safeValue = '"' . addslashes($value) . '"';
                        
                        $pattern = "/^{$key}=.*/m";

                        if (preg_match($pattern, $content)) {
                            $content = preg_replace($pattern, "{$key}={$safeValue}", $content);
                        } else {
                            $content .= "\n{$key}={$safeValue}";
                        }
                    }

                    // Gereksiz satır başlarını temizle ve dosyayı güncelle
                    File::put($envPath, trim($content) . "\n");

                    // Önbelleği temizle (Yeni ayarların anında config() üzerinden okunması için)
                    // Not: Forge deployment scriptlerinde genelde 'config:cache' olduğu için 
                    // anlık yansıma adına Cache temizliği faydalı olabilir.
                }
            } catch (\Exception $e) {
                Log::error("Setting Model - ENV Yazım Hatası: " . $e->getMessage());
            }
        });
    }

    /**
     * Instagram gönderilerini çeker.
     */
    public function getInstagramPosts($limit = 6)
    {
        if (!$this->instagram_access_token) {
            return [];
        }

        return Cache::remember('insta_posts', 3600, function () use ($limit) {
            try {
                $url = "https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url,permalink,thumbnail_url,timestamp&access_token={$this->instagram_access_token}";
                
                // file_get_contents yerine daha güvenli hata kontrolü
                $response = @file_get_contents($url);
                
                if ($response === false) {
                    Log::warning("Instagram API - Veri çekilemedi. Token geçersiz olabilir.");
                    return [];
                }

                $data = json_decode($response, true);
                return array_slice($data['data'] ?? [], 0, $limit);
            } catch (\Exception $e) {
                Log::warning("Instagram API Hatası: " . $e->getMessage());
                return [];
            }
        });
    }
}