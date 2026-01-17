<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\SitemapGenerator;

class GenerateSitemap extends Command
{
    // Komutun terminaldeki adı
    protected $signature = 'sitemap:generate';

    protected $description = 'Sitemap dosyasını otomatik oluşturur';

    public function handle()
    {
        // APP_URL üzerinden taramaya başlar ve public/sitemap.xml olarak kaydeder
        SitemapGenerator::create(config('app.url'))
            ->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap başarıyla oluşturuldu!');
    }
}