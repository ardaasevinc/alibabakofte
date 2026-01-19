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

            {{-- Tamamen İzole Edilmiş Pagination --}}
            <div style="text-align: center; padding: 60px 0; clear: both; width: 100%;">
                <div
                    style="display: inline-block; border: 1px solid #ddd; border-radius: 4px; overflow: hidden; background: #fff;">

                    {{-- Önceki Sayfa --}}
                    @if ($latestBlogs->onFirstPage())
                        <span
                            style="display: inline-block; padding: 12px 18px; color: #ccc; border-right: 1px solid #ddd;">&lsaquo;</span>
                    @else
                        <a href="{{ $latestBlogs->previousPageUrl() }}"
                            style="display: inline-block; padding: 12px 18px; color: #333; text-decoration: none; border-right: 1px solid #ddd; background: #fff;">&lsaquo;</a>
                    @endif

                    {{-- Sayfa Numaraları --}}
                    @foreach ($latestBlogs->getUrlRange(1, $latestBlogs->lastPage()) as $page => $url)
                        @if ($page == $latestBlogs->currentPage())
                            <span
                                style="display: inline-block; padding: 12px 18px; background-color: #5584b4; color: #fff; border-right: 1px solid #ddd; font-weight: bold;">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                                style="display: inline-block; padding: 12px 18px; color: #333; text-decoration: none; border-right: 1px solid #ddd; background: #fff;">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Sonraki Sayfa --}}
                    @if ($latestBlogs->hasMorePages())
                        <a href="{{ $latestBlogs->nextPageUrl() }}"
                            style="display: inline-block; padding: 12px 18px; color: #333; text-decoration: none; background: #fff;">&rsaquo;</a>
                    @else
                        <span style="display: inline-block; padding: 12px 18px; color: #ccc;">&rsaquo;</span>
                    @endif

                </div>
            </div>
        </div>
    @endif
@endsection