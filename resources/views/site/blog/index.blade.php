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

                    {{-- Blog Görseli --}}
                    @if($blog->image)
                    <div class="row mb40 mb-xs-24">
                        <div class="col-sm-10 col-sm-offset-1 text-center">
                            <a href="{{ route('site.blog.detail', $blog->slug) }}">
                                <img alt="{{ $blog->title }}" src="{{ asset('uploads/' . $blog->image) }}" class="img-responsive" style="display:inline-block;" />
                            </a>
                        </div>
                    </div>
                    @endif

                    {{-- Blog İçeriği (Str::limit ile kısıtlanmış) --}}
                    <div class="row mb40 mb-xs-24">
                        <div class="col-sm-8 col-sm-offset-2 text-center">
                            <div class="blog-excerpt">
                                {{-- HTML etiketlerini temizleyip 250 karakter ile sınırlıyoruz --}}
                                {{ Str::limit(strip_tags($blog->desc), 250, '...') }}
                            </div>
                            <a class="btn btn-sm mt24" href="{{ route('site.blog.detail', $blog->slug) }}">Devamını Oku</a>
                        </div>
                    </div>
                </div>
            </section>
            <hr style="margin: 64px 0 0 0; border-color: #eee;">
        @endforeach

        {{-- Pagination Alanı --}}
        <section class="pt64 pb64">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        {{ $latestBlogs->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endif
@endsection