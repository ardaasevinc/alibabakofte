@extends('site.layouts.site')

@section('css')
    <style>
        /* Pagination Container */
        .custom-pagination-area {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0;
            /* Kutular arası boşluğu sıfırlayıp border'ları birleştiriyoruz */
            margin: 40px 0;
            font-family: 'alt-font', sans-serif;
        }

        .custom-pagination-area ul {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            border: 1px solid #e0e0e0;
            /* Dış çerçeve */
            border-radius: 4px;
            overflow: hidden;
        }

        .custom-pagination-area li {
            border-right: 1px solid #e0e0e0;
        }

        .custom-pagination-area li:last-child {
            border-right: none;
        }

        .custom-pagination-area li a,
        .custom-pagination-area li span {
            display: block;
            padding: 12px 20px;
            min-width: 45px;
            text-align: center;
            color: #333;
            text-decoration: none;
            background: #fff;
            font-size: 16px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        /* Aktif Sayfa (Görseldeki Mavi Renk) */
        .custom-pagination-area li.active span {
            background-color: #5584b4 !important;
            /* Görseldeki mavi tonu */
            color: #fff !important;
            cursor: default;
        }

        /* Hover Durumu */
        .custom-pagination-area li a:hover {
            background-color: #f9f9f9;
            color: #5584b4;
        }

        /* Devre Dışı (Oklar) */
        .custom-pagination-area li.disabled span {
            color: #ccc;
            cursor: not-allowed;
        }
    </style>
@endsection

@section('content')
    @if($latestBlogs && $latestBlogs->count() > 0)
        <div class="main-container">
            @foreach($latestBlogs as $blog)
                <section class="pb0">
                    <div class="container">
                        <div class="row mb40 mb-xs-24">
                            <div class="col-sm-12 text-center">
                                <div class="ribbon mb24">
                                    <h6 class="uppercase mb0">
                                        {{ $blog->category?->title }}
                                    </h6>
                                    <span class="mb24">{{ $blog->created_at->translatedFormat('d F Y') }}</span>
                                </div>
                                <a href="{{ route('site.blog.detail', $blog->slug) }}">
                                    <h2 class="alt-font mb16">{{ $blog->title }}</h2>
                                </a>
                            </div>
                        </div>

                        @if($blog->image)
                            <div class="row mb40 mb-xs-24">
                                <div class="col-sm-10 col-sm-offset-1 text-center">
                                    <a href="{{ route('site.blog.detail', $blog->slug) }}">
                                        <img alt="{{ $blog->title }}" src="{{ asset('uploads/' . $blog->image) }}"
                                            class="img-responsive" style="display:inline-block; height:600px; object-fit: cover;" />
                                    </a>
                                </div>
                            </div>
                        @endif

                        <div class="row mb40 mb-xs-24">
                            <div class="col-sm-8 col-sm-offset-2 text-center">
                                <div class="blog-excerpt">
                                    {{ Str::limit(strip_tags($blog->desc), 250, '...') }}
                                </div>
                                <a class="btn btn-sm mt24" href="{{ route('site.blog.detail', $blog->slug) }}">Devamını Oku</a>
                            </div>
                        </div>
                    </div>
                </section>
                <hr style="margin: 64px 0 0 0; border-color: #eee;">
            @endforeach

            {{-- Yeni ve Sorunsuz Pagination Alanı --}}
            <div class="custom-pagination-area">
                <ul>
                    {{-- Önceki Sayfa --}}
                    @if ($latestBlogs->onFirstPage())
                        <li class="disabled"><span>&lsaquo;</span></li>
                    @else
                        <li><a href="{{ $latestBlogs->previousPageUrl() }}">&lsaquo;</a></li>
                    @endif

                    {{-- Sayfalar --}}
                    @foreach ($latestBlogs->getUrlRange(1, $latestBlogs->lastPage()) as $page => $url)
                        @if ($page == $latestBlogs->currentPage())
                            <li class="active"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    {{-- Sonraki Sayfa --}}
                    @if ($latestBlogs->hasMorePages())
                        <li><a href="{{ $latestBlogs->nextPageUrl() }}">&rsaquo;</a></li>
                    @else
                        <li class="disabled"><span>&rsaquo;</span></li>
                    @endif
                </ul>
            </div>
        </div>
    @endif
@endsection