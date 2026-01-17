<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;

class GenerateSitemap extends Command
{
    // Komutun terminaldeki adı
    protected $signature = 'sitemap:generate';

    protected $description = 'Sitemap dosyasını otomatik oluşturur';

    // app/Console/Commands/GenerateSitemap.php

public function handle()
{
    // Sitemap nesnesini oluştur
    $sitemap = \Spatie\Sitemap\Sitemap::create();

    // 1. Ana sayfaları ekle
    $sitemap->add('/')
            ->add('/blog')
            ->add('/menu');
            

    // 2. Dinamik Blog Yazılarını Veritabanından Çek ve Ekle
    // Not: Model adın 'Blog' veya 'Post' ise ona göre güncelle
    \App\Models\Blog::where('is_published', true)->get()->each(function ($post) use ($sitemap) {
        $sitemap->add("/blog/{$post->slug}");
    });

    // Dosyayı kaydet
    $sitemap->writeToFile(public_path('sitemap.xml'));

    $this->info('Sitemap blog yazılarıyla birlikte güncellendi.');
}
}