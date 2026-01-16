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
                'MAIL_MAILER' => $setting->mail_mailer,
                'MAIL_HOST' => $setting->mail_host,
                'MAIL_PORT' => $setting->mail_port,
                'MAIL_USERNAME' => $setting->mail_username,
                'MAIL_PASSWORD' => $setting->mail_password,
                'MAIL_FROM_ADDRESS' => $setting->mail_from_address,
                'MAIL_FROM_NAME' => $setting->mail_from_name,
            ];

            $envPath = base_path('.env');
            if (File::exists($envPath)) {
                $content = File::get($envPath);
                foreach ($envMapping as $key => $value) {
                    // Değer boşsa veya null ise çift tırnak içinde boş bırakabiliriz
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
}