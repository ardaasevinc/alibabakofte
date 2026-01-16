<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\InstagramPost;
use Carbon\Carbon;

class FetchInstagramPosts extends Command
{
    protected $signature = 'instagram:fetch';
    protected $description = 'Instagram API üzerinden gönderileri çeker ve videoları orijinal formatıyla kaydeder';

    public function handle()
    {
        $token = config('services.instagram.access_token');

        // 1. Token Yenileme (Bağlantının kopmaması için 60 günlük süreyi tazeler)
        Http::get("https://graph.instagram.com/refresh_access_token", [
            'grant_type' => 'ig_refresh_token',
            'access_token' => $token
        ]);

        // 2. Medya Verilerini Çekme (thumbnail_url dahil)
        $response = Http::get("https://graph.instagram.com/me/media", [
            'fields' => 'id,caption,media_type,media_url,permalink,timestamp,thumbnail_url',
            'access_token' => $token
        ]);

        if ($response->successful()) {
            $data = $response->json('data');

            foreach ($data as $post) {
                // Videolar için media_url asıl video dosyasını (.mp4) verir.
                // Blade tarafında <video> etiketi bunu oynatabilir.
                InstagramPost::updateOrCreate(
                    ['instagram_id' => $post['id']],
                    [
                        'media_type' => $post['media_type'],
                        'media_url' => $post['media_url'],
                        'permalink' => $post['permalink'],
                        'caption' => $post['caption'] ?? null,
                        'posted_at' => Carbon::parse($post['timestamp']),
                    ]
                );
            }

            $this->info(count($data) . " adet içerik (video ve resim) başarıyla senkronize edildi.");
        } else {
            $this->error("Hata: " . $response->body());
        }
    }
}