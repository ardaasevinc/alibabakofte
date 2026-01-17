<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class Setting extends Model
{
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::saved(function ($setting) {
            // Sadece basit metin olanları .env'ye gönderiyoruz
            // Pixel ve Analytics gibi uzun SCRIPT kodlarını .env'ye YAZMIYORUZ (DB'de kalıyor)
            $envMapping = [
                'APP_URL' => $setting->app_url,
                'APP_ENV' => $setting->app_env,
                'APP_DEBUG' => $setting->app_debug ? 'true' : 'false',
                'MAIL_HOST' => $setting->mail_host,
                'MAIL_PORT' => $setting->mail_port,
                'MAIL_USERNAME' => $setting->mail_username,
                'MAIL_PASSWORD' => $setting->mail_password,
                'MAIL_FROM_ADDRESS' => $setting->mail_from_address,
                'MAIL_FROM_NAME' => $setting->mail_from_name,
                'FACEBOOK_ACCESS_TOKEN' => $setting->facebook_access_token,
            ];

            try {
                $envPath = base_path('.env');
                if (File::exists($envPath)) {
                    $content = File::get($envPath);
                    foreach ($envMapping as $key => $value) {
                        $safeValue = '"' . str_replace('"', '\"', $value) . '"';
                        $pattern = "/^{$key}=.*/m";
                        if (preg_match($pattern, $content)) {
                            $content = preg_replace($pattern, "{$key}={$safeValue}", $content);
                        } else {
                            $content .= "\n{$key}={$safeValue}";
                        }
                    }
                    File::put($envPath, $content);
                }
            } catch (\Exception $e) {
                // .env yazamazsa bile DB kaydı durmasın diye log atıp geçiyoruz
                Log::error("ENV Yazım Hatası: " . $e->getMessage());
            }
        });
    }

    public function getInstagramPosts($limit = 6)
    {
        if (!$this->instagram_access_token) return [];
        return \Illuminate\Support\Facades\Cache::remember('insta_posts', 3600, function () use ($limit) {
            try {
                $url = "https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url,permalink,thumbnail_url,timestamp&access_token={$this->instagram_access_token}";
                $response = file_get_contents($url);
                $data = json_decode($response, true);
                return array_slice($data['data'] ?? [], 0, $limit);
            } catch (\Exception $e) { return []; }
        });
    }
}