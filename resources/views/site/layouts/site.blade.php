<!doctype html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <title>Meşhur Çatalcalı Ali Baba Köfte Salonu | 1997'den Beri Gelen Lezzet</title>
    <meta name="viewport" content="width=device-width, initial-scale=0.9">
    <link href="{{ asset('site/css/bootstrap.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('site/css/themify-icons.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('site/css/flexslider.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('site/css/theme.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link href="{{ asset('site/css/custom.css') }}" rel="stylesheet" type="text/css" media="all" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('site/alibaba/logos/favicon.svg') }}">
    <link rel="alternate icon" type="image/png" href="{{ asset('site/alibaba/logos/favicon.png') }}">
    <link rel="mask-icon" href="{{ asset('site/alibaba/logos/favicon.svg') }}" color="#000000">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('site/alibaba/logos/favicon.svg') }}">


    <link href='https://fonts.googleapis.com/css?family=Montserrat%7CInconsolata:400,700%7CPathway+Gothic+One'
        rel='stylesheet' type='text/css'>
    <meta name="description"
        content="Çatalca'nın meşhur tarihi köftecisi Ali Baba Köfte Salonu. 1997'den beri Çatalca merkezde değişmeyen reçetesiyle hizmet veren Ali Baba'nın eşsiz köftelerini deneyin.">
    <meta name="keywords"
        content="çatalca köfte, meşhur çatalca köftecisi, ali baba köfte çatalca, tarihi çatalca köftesi, çatalca merkez restoran, çatalca yemek yerleri, alibaba köfte, çatalca köfte salonu, ali baba köfte salonu">
    <meta name="author" content="Ali Baba Köfte Salonu">

    <meta property="og:title" content="Meşhur Çatalcalı Ali Baba Köfte Salonu">
    <meta property="og:description" content="1997'den beri Çatalca'da köftenin Ali Babası. Gerçek lezzet, tarihi doku.">
    <meta property="og:image" content="{{ asset('site/alibaba/logos/logo-white.svg') }}">
    <meta property="og:url" content="https://alibabakofte.com.tr">
    <meta property="og:type" content="website">
    <meta name="robots" content="index, follow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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