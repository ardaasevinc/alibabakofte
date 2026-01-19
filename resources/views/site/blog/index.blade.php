@extends('site.layouts.site')

@section('css')
    <style>
        /* Pagination Tasarımı */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            gap: 8px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .pagination-wrapper li a,
        .pagination-wrapper li span {
            display: inline-block;
            padding: 10px 18px;
            border: 1px solid #e0e0e0;
            color: #333;
            text-decoration: none;
            background: #fff;
            transition: all 0.2s ease;
            font-family: 'alt-font', sans-serif;
        }

        /* Aktif Sayfa (Ekran görüntüsündeki mavi tonu) */
        .pagination-wrapper li.active span {
            background-color: #5584b4;
            color: #fff;
            border-color: #5584b4;
        }

        .pagination-wrapper li a:hover {
            background-color: #f8f8f8;
            border-color: #ccc;
        }

        /* Ok işaretleri için ekstra stil */
        .pagination-wrapper li.disabled span {
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

                        {{-- Blog Görseli --}}
                        @if($blog->image)
                        <div class="row mb40 mb-xs-24">
                            <div class="col-sm-10 col-sm-offset-1 text-center">
                                <a href="{{ route('site.blog.detail', $blog->slug) }}">
                                    <img alt="{{ $blog->title }}" src="{{ asset('uploads/' . $blog->image) }}" class="img-responsive" style="display:inline-block; height:600px;" />
                                </a>
                            </div>
                        </div>
                        @endif

                        {{-- Blog İçeriği --}}
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

                        {{-- Özel Pagination Alanı --}}
                        <section class="pt64 pb64">
                            <div class="container">
                                <div class="row">
                                    <div class="col-sm-12 text-center">
                                        <ul class="pagination-wrapper">
                                            {{-- Geri Butonu --}}
                                            @if ($latestBlogs->onFirstPage())
                                                <li class="disabled"><span>&lsaquo;</span></li>
                                            @else
                                                <li><a href="{{ $latestBlogs->previousPageUrl() }}">&lsaquo;</a></li>
                                            @endif

                                            {{-- Sayfa Numaraları --}}
                                            @foreach ($latestBlogs->getUrlRange(1, $latestBlogs->lastPage()) as $page => $url)
                                                @if ($page == $latestBlogs->currentPage())
                                                    <li class="active"><span>{{ $page }}</span></li>
                                                @else
                                                    <li><a href="{{ $url }}">{{ $page }}</a></li>
                                                @endif
                                            @endforeach

                                            {{-- İleri Butonu --}}
                                            @if ($latestBlogs->hasMorePages())
                                                <li><a href="{{ $latestBlogs->nextPageUrl() }}">&rsaquo;</a></li>
                                            @else
                                                <li class="disabled"><span>&rsaquo;</span></li>
                                            @endif
                                        </ul>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    @endif
@endsection