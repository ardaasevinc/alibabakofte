<?php


namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\InstagramPost;
use Carbon\Carbon;

class FetchInstagramPosts extends Command
{
    // Komutu terminalden çalıştırma adı
    protected $signature = 'instagram:fetch';
    protected $description = 'Instagram API üzerinden gönderileri çeker ve tokenı yeniler';

public function handle()
{
    // config'den token'ı alıyoruz (Artık çalıştığına eminiz)
    $token = config('services.instagram.access_token');

    $response = Http::get("https://graph.instagram.com/me/media", [
        'fields' => 'id,caption,media_type,media_url,permalink,timestamp',
        'access_token' => $token
    ]);

    if ($response->successful()) {
        $data = $response->json('data');
        
        foreach ($data as $post) {
            InstagramPost::updateOrCreate(
                ['instagram_id' => $post['id']], // Eğer bu ID varsa güncelle, yoksa yeni oluştur
                [
                    'media_type'   => $post['media_type'],
                    'media_url'    => $post['media_url'],
                    'permalink'    => $post['permalink'],
                    'caption'      => $post['caption'] ?? null,
                    'posted_at'    => \Illuminate\Support\Carbon::parse($post['timestamp']),
                    // 'is_published' default migration'da neyse o kalır (false yapmıştık)
                ]
            );
        }

        $this->info(count($data) . " adet gönderi başarıyla işlendi.");
    } else {
        $this->error("Hata: " . $response->body());
    }
}
}