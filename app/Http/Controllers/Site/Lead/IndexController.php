<?php

namespace App\Http\Controllers\Site\Lead;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;
use App\Services\MetaCapiService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class IndexController extends Controller
{
    public function whatsapp()
    {
        // Sabit telefon numarası veya dinamik olarak ayarlanabilir
        $phone = '905331959477'; 
        return $this->processLead('meta-whatsapp', "https://wa.me/{$phone}");
    }

    public function menu()
    {
        // Menü rotasına yönlendirme
        return $this->processLead('meta-menu', route('site.menu.index'));
    }

    private function processLead($buttonId, $targetUrl)
    {
        // 1. GÜVENLİK: Bot ve Prefetch (Ön yükleme) engelleme
        if (
            request()->header('X-Purpose') == 'preview' ||
            request()->header('X-Moz') == 'prefetch' ||
            request()->header('Purpose') == 'prefetch'
        ) {
            return redirect()->to($targetUrl);
        }

        $previousUrl = url()->previous();
        $currentUrl = url()->current();

        // 2. GÜVENLİK: Sayfa yenileme veya boş yönlendirme koruması
        if ($previousUrl === $currentUrl || empty($previousUrl)) {
            return redirect()->to($targetUrl);
        }

        // 3. GÜVENLİK: Mükerrer Tıklama Kilidi (Aynı IP ve buton için 10 saniye)
        $lockKey = 'lead_lock_' . md5(request()->ip() . $buttonId);
        if (Cache::has($lockKey)) {
            return redirect()->to($targetUrl);
        }
        Cache::put($lockKey, true, 10);

        // Olay Eşleştirme (Deduplication) için benzersiz ID oluşturma
        // Bu ID hem CAPI'ye hem (isteğe bağlı) tarayıcıya giderse Meta olayları birleştirir.
        $eventId = 'ab_' . uniqid() . '_' . time();

        // URL parametrelerini ayıkla (UTM takibi için)
        parse_str(parse_url($previousUrl, PHP_URL_QUERY) ?? '', $urlQueries);

        try {
            // VERİTABANI KAYDI: Önce kaydı yapıyoruz ki ID oluşsun.
            $lead = Lead::create([
                'type' => ($buttonId === 'meta-whatsapp') ? 'whatsapp' : 'menu',
                'event_id' => $eventId,
                'utm_source' => $urlQueries['utm_source'] ?? session('utm_source', 'direct'),
                'utm_campaign' => $urlQueries['utm_campaign'] ?? session('utm_campaign', '-'),
                'fbclid' => $urlQueries['fbclid'] ?? session('fbclid'),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'payload' => [
                    'came_from' => $previousUrl,
                    'button_id' => $buttonId,
                    'utm_medium' => $urlQueries['utm_medium'] ?? 'reklam',
                ],
            ]);

            // META CAPI GÖNDERİMİ
            // Parametreler: 1.Olay Adı, 2.Kullanıcı Verileri, 3.Olay ID, 4.Test Kodu
            // NOT: Test bittikten sonra 'TEST24572' yerine null yazmayı unutmayın.
            MetaCapiService::sendEvent(
                'Lead',
                [
                    'external_id' => hash('sha256', (string) $lead->id),
                    'fbc' => $lead->fbclid ? "fb.1." . time() . "." . $lead->fbclid : null,
                    'event_source_url' => $previousUrl,
                ],
                $eventId,
                'TEST24572' // Sizin Meta panelindeki test kodunuz
            );

        } catch (\Exception $e) {
            // Hata olursa logla ama kullanıcıyı bekletme
            Log::error("Ali Baba Lead Hatası: " . $e->getMessage());
        }

        // 4. SONUÇ: Kullanıcıyı hedef URL'ye (WhatsApp veya Menü) uçur.
        return redirect()->to($targetUrl);
    }
}