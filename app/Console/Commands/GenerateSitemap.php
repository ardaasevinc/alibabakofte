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
    $sitemap = \Spatie\Sitemap\Sitemap::create();

    // Ana Sayfalar
    $sitemap->add(\Spatie\Sitemap\Tags\Url::create('/')->setPriority(1.0))
            ->add(\Spatie\Sitemap\Tags\Url::create('/blog')->setPriority(0.8))
            ->add(\Spatie\Sitemap\Tags\Url::create('/menu')->setPriority(0.8));

    // Dinamik Blog Yazılarını Ekle (Başlık değil, URL olarak)
    \App\Models\Blog::all()->each(function (\App\Models\Blog $blog) use ($sitemap) {
        $sitemap->add(\Spatie\Sitemap\Tags\Url::create("/blog/{$blog->slug}")
            ->setLastModificationDate($blog->updated_at)
            ->setChangeFrequency(\Spatie\Sitemap\Tags\Url::CHANGE_FREQUENCY_WEEKLY)
            ->setPriority(0.6));
    });

    $sitemap->writeToFile(public_path('sitemap.xml'));
}
}