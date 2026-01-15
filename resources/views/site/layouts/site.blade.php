<!doctype html>
<html lang="tr">
    <head>
        <meta charset="utf-8">
        <title>Meşhur Çatalcalı Ali Baba Köfte</title>
        <meta name="viewport" content="width=device-width, initial-scale=0.9">
        <link href="{{ asset('site/css/bootstrap.css') }}" rel="stylesheet" type="text/css" media="all" />
        <link href="{{ asset('site/css/themify-icons.css') }}" rel="stylesheet" type="text/css" media="all" />
        <link href="{{ asset('site/css/flexslider.css') }}" rel="stylesheet" type="text/css" media="all" />
		<link href="{{ asset('site/css/theme.css') }}" rel="stylesheet" type="text/css" media="all" />
		<link href="{{ asset('site/css/custom.css') }}" rel="stylesheet" type="text/css" media="all" />
        <link href='https://fonts.googleapis.com/css?family=Montserrat%7CInconsolata:400,700%7CPathway+Gothic+One' rel='stylesheet' type='text/css'>  
    </head>
    <body>
        
        @include('site.components.header')
        <div class="main-container">
            @yield('content')
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