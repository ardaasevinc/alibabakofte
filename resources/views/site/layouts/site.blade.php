<!doctype html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <title>
        @if(isset($blog))
            {{ $blog->title }} | {{ $settings?->title_suffix ?? 'Meşhur Çatalcalı Ali Baba Köfte' }}
        @else
            {{ $settings?->meta_title ?? 'Meşhur Çatalcalı Ali Baba Köfte Salonu | 1997\'den Beri Gelen Lezzet' }}
        @endif
    </title>
    <meta name="facebook-domain-verification" content="5evuw3kv4nmwq9w466zked48jo6md7" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="description"
        content="{{ isset($blog) ? Str::limit(strip_tags($blog->desc), 160) : ($settings?->meta_desc ?? 'Çatalca\'nın meşhur tarihi köftecisi Ali Baba Köfte Salonu.') }}">

    <meta name="keywords"
        content="{{ (isset($blog) && is_array($blog->tags)) ? implode(', ', $blog->tags) : ($settings->meta_keywords ?? 'çatalca köfte, meşhur çatalca köftecisi, ali baba köfte çatalca') }}">

    <meta name="author" content="selquor.com">
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="{{ isset($blog) ? $blog->title : $settings?->meta_title }}">
    <meta property="og:description"
        content="{{ isset($blog) ? Str::limit(strip_tags($blog->desc), 160) : $settings?->meta_desc }}">
    <meta property="og:image"
        content="{{ (isset($blog) && $blog->image) ? asset('uploads/' . $blog->image) : ($settings?->logo_dark ? asset('uploads/' . $settings?->logo_dark) : asset('site/alibaba/logos/logo-white.svg')) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="{{ isset($blog) ? 'article' : 'website' }}">

    <link href="{{ asset('site/css/bootstrap.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('site/css/themify-icons.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('site/css/flexslider.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('site/css/theme.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('site/css/custom.css') }}" rel="stylesheet" type="text/css" media="all" />

    @if($settings?->favicon)
        <link rel="icon" type="image/png" href="{{ asset('uploads/' . $settings?->favicon) }}">
        <link rel="apple-touch-icon" href="{{ asset('uploads/' . $settings?->favicon) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('site/alibaba/logos/favicon.svg') }}">
        <link rel="alternate icon" type="image/png" href="{{ asset('site/alibaba/logos/favicon.png') }}">
    @endif

    <link href='https://fonts.googleapis.com/css?family=Montserrat%7CInconsolata:400,700%7CPathway+Gothic+One'
        rel='stylesheet' type='text/css'>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#25262e">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Ali Baba Köfte">

    <link rel="apple-touch-icon" href="{{ asset('site/alibaba/icons/icon-192x192.png') }}">



<script>
    !function (f, b, e, v, n, t, s) {
        if (f.fbq) return; n = f.fbq = function () {
            n.callMethod ?
            n.callMethod.apply(n, arguments) : n.queue.push(arguments)
        }; if (!f._fbq) f._fbq = n;
        n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = []; t = b.createElement(e); t.async = !0;
        t.src = v; s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s)
    }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

    function getCookie(name) {
        let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        return match ? match[2] : null;
    }

    // 1. GELİŞMİŞ EŞLEŞTİRME (Advanced Matching)
    // Auth kullanıcısı varsa verilerini hashleyerek veriyoruz.
    fbq('init', '{{ config("services.meta.pixel_id") }}', {
        @if(auth()->check())
            em: '{{ hash("sha256", strtolower(trim(auth()->user()->email))) }}',
            @if(auth()->user()->phone)
                ph: '{{ hash("sha256", preg_replace("/[^0-9]/", "", auth()->user()->phone)) }}',
            @endif
        @endif
        external_id: '{{ hash("sha256", (string) session()->getId()) }}',
        fbp: getCookie('_fbp'),
        fbc: getCookie('_fbc')
    });

    fbq('track', 'PageView');

    // 2. LEAD İŞLEME FONKSİYONU
    function handleLead(type, targetUrl) {
        // PHP tarafıyla birebir uyumlu saniye bazlı ID
        const timestamp = Math.floor(Date.now() / 1000);
        const randomStr = Math.random().toString(36).substr(2, 6);
        const eventId = 'lead_' + randomStr + '_' + timestamp;

        // Fiyat Hatası Çözümü: Tarayıcı tarafında da ufak bir dinamizm ekliyoruz
        // Controller'daki rand() mantığına yakın (1.00 - 1.50 arası)
        const dynamicValue = (type === 'meta-whatsapp' ? 1.50 : 1.00) + (Math.floor(Math.random() * 50) / 100);

        // Meta Pixel Olayı
        if (typeof fbq === 'function') {
            fbq('track', 'Lead', {
                content_name: type,
                value: dynamicValue,
                currency: 'TRY'
            }, { eventID: eventId }); // EVENT_ID Eşleşmesi Burada
        }

        // Meta'nın olayı algılaması için çok kısa bir bekleme (500ms ideal)
        setTimeout(function () {
            const separator = targetUrl.indexOf('?') !== -1 ? '&' : '?';
            // ID'yi Controller'a taşıyoruz
            window.location.href = targetUrl + separator + 'meta_event_id=' + eventId;
        }, 400); 
    }
</script>



    
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings->google_analytics_code }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', '{{ $settings->google_analytics_code }}');
    </script>
</head>

<body>

    @include('site.components.header')
    <div class="main-container">
        @yield('content')
        @include('site.components.whatsapp')
        @include('site.components.footer')
    </div>

   

    <script src="{{ asset('site/js/jquery.min.js') }}"></script>
    <script src="{{ asset('site/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('site/js/flexslider.min.js') }}"></script>
    <script src="{{ asset('site/js/twitterfetcher.min.js') }}"></script>
    <script src="{{ asset('site/js/spectragram.min.js') }}"></script>
    <script src="{{ asset('site/js/smooth-scroll.min.js') }}"></script>
    <script src="{{ asset('site/js/parallax.min.js') }}"></script>
    <script src="{{ asset('site/js/scripts.js') }}"></script>


</body>

</html>