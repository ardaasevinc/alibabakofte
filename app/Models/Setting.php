<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Setting extends Model
{
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::saved(function ($setting) {
            $envMapping = [
                'APP_URL' => $setting->app_url,
                'APP_ENV' => $setting->app_env,
                'APP_DEBUG' => $setting->app_debug ? 'true' : 'false',
                'INSTAGRAM_ACCESS_TOKEN' => $setting->instagram_access_token,
                'INSTAGRAM_APP_SECRET' => $setting->instagram_app_secret,
                'MAIL_HOST' => $setting->mail_host,
                'MAIL_PORT' => $setting->mail_port,
                'MAIL_USERNAME' => $setting->mail_username,
                'MAIL_PASSWORD' => $setting->mail_password,
                'MAIL_FROM_ADDRESS' => $setting->mail_from_address,
                'MAIL_FROM_NAME' => $setting->mail_from_name,
                // ANALİZ AYARLARI BURAYA EKLENDİ
                'FACEBOOK_PIXEL_ID' => $setting->facebook_pixel_id,
                'FACEBOOK_ACCESS_TOKEN' => $setting->facebook_access_token,
                'GOOGLE_ANALYTICS_ID' => $setting->google_analytics_id,
                'GOOGLE_TAG_MANAGER_ID' => $setting->google_tag_manager_id,
            ];

            $envPath = base_path('.env');
            if (File::exists($envPath)) {
                $content = File::get($envPath);
                foreach ($envMapping as $key => $value) {
                    $value = '"' . $value . '"';
                    $pattern = "/^{$key}=.*/m";
                    if (preg_match($pattern, $content)) {
                        $content = preg_replace($pattern, "{$key}={$value}", $content);
                    } else {
                        $content .= "\n{$key}={$value}";
                    }
                }
                File::put($envPath, $content);
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