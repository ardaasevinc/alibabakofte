@extends('site.layouts.site')

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
                                        <img alt="{{ $blog->title }}" src="{{ asset('uploads/' . $blog->image) }}"
                                            class="img-responsive" style="display:inline-block; height:600px; object-fit: cover;" />
                                    </a>
                                </div>
                            </div>
                        @endif
                        <span class="mb24 mt24 text-center" style="display: block; width: 100%;">
    {{ $blog->created_at->translatedFormat('d F Y') }}
</span>

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

            @endforeach

            {{-- Sadece Rakam ve İkonlardan Oluşan Sade Pagination --}}
            <div style="text-align: center; ">
                <div style="display: inline-block;">

                    {{-- Önceki Sayfa İkonu --}}
                    @if ($latestBlogs->onFirstPage())
                        <span style="padding: 0 10px; color: #ccc; cursor: not-allowed; font-size: 18px;">&lsaquo;</span>
                    @else
                        <a href="{{ $latestBlogs->previousPageUrl() }}"
                            style="padding: 0 10px; color: #333; text-decoration: none; font-size: 18px;">&lsaquo;</a>
                    @endif

                    {{-- Sayfa Numaraları --}}
                    @foreach ($latestBlogs->getUrlRange(1, $latestBlogs->lastPage()) as $page => $url)
                        @if ($page == $latestBlogs->currentPage())
                            {{-- Seçili Sayfa: Kırmızı ve Temiz --}}
                            <span style="padding: 0 10px; color: red; font-weight: bold; font-size: 18px;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                style="padding: 0 10px; color: #333; text-decoration: none; font-size: 18px;">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Sonraki Sayfa İkonu --}}
                    @if ($latestBlogs->hasMorePages())
                        <a href="{{ $latestBlogs->nextPageUrl() }}"
                            style="padding: 0 10px; color: #333; text-decoration: none; font-size: 18px;">&rsaquo;</a>
                    @else
                        <span style="padding: 0 10px; color: #ccc; cursor: not-allowed; font-size: 18px;">&rsaquo;</span>
                    @endif

                </div>
            </div>
        </div>
    @endif
@endsection