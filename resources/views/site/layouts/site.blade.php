<!doctype html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    {{-- SEO Başlık --}}
    <title>{{ $settings?->meta_title ?? 'Meşhur Çatalcalı Ali Baba Köfte Salonu | 1997\'den Beri Gelen Lezzet' }}
    </title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- SEO Meta Etiketleri --}}
    <meta name="description"
        content="{{ $settings?->meta_desc ?? 'Çatalca\'nın meşhur tarihi köftecisi Ali Baba Köfte Salonu. 1997\'den beri Çatalca merkezde değişmeyen reçetesiyle hizmet veren Ali Baba.' }}">
    <meta name="keywords"
        content="{{ $settings->meta_keywords ?? 'çatalca köfte, meşhur çatalca köftecisi, ali baba köfte çatalca, alibaba köfte' }}">
    <meta name="author" content="Ali Baba Köfte Salonu">
    <meta name="robots" content="index, follow">

    {{-- Open Graph (Sosyal Medya Paylaşım) --}}
    <meta property="og:title" content="{{ $settings?->meta_title }}">
    <meta property="og:description" content="{{ $settings?->meta_desc }}">
    <meta property="og:image"
        content="{{ $settings?->logo_dark ? asset('uploads/' . $settings?->logo_dark) : asset('site/alibaba/logos/logo-white.svg') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    {{-- CSS Dosyaları --}}
    <link href="{{ asset('site/css/bootstrap.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('site/css/themify-icons.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('site/css/flexslider.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('site/css/theme.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('site/css/custom.css') }}" rel="stylesheet" type="text/css" media="all" />

    {{-- Favicon - Dinamik --}}
    @if($settings?->favicon)
        <link rel="icon" type="image/png" href="{{ asset('uploads/' . $settings?->favicon) }}">
        <link rel="apple-touch-icon" href="{{ asset('uploads/' . $settings?->favicon) }}">
    @else
        <link rel="icon" type="image/svg+xml" href="{{ asset('site/alibaba/logos/favicon.svg') }}">
        <link rel="alternate icon" type="image/png" href="{{ asset('site/alibaba/logos/favicon.png') }}">
    @endif

    {{-- Fontlar --}}
    <link href='https://fonts.googleapis.com/css?family=Montserrat%7CInconsolata:400,700%7CPathway+Gothic+One'
        rel='stylesheet' type='text/css'>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#25262e">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Ali Baba Köfte">
    
    <link rel="apple-touch-icon" href="{{ asset('site/alibaba/icons/icon-192x192.png') }}">

    <script>
        !function (f, b, e, v, n, t, s) {
            if (f.fbq) return; n = f.fbq = function () {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
            n.queue = []; t = b.createElement(e); t.async = !0;
            t.src = v; s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script',
            'https://connect.facebook.net/en_US/fbevents.js');

        fbq('init', '{{ $settings->facebook_pixel_code }}');
        fbq('track', 'PageView');
    </script>
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $settings->google_analytics_code }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', '{{ $settings->google_analytics_code }}');
    </script>

    {{-- UTM Takibi İçerik Kontrolü --}}
    @if(request()->has('utm_content'))
    @endif


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