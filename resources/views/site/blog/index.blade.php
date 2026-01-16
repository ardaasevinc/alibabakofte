
@extends('site.layouts.site')

@section('content')
@if($latestBlogs)
    <div class="main-container">
        @if($latestBlogs && $latestBlogs->count() > 0)
            @foreach($latestBlogs as $blog)
                <section>
                    <div class="container">
                        <div class="row mb40 mb-xs-24">
                            <div class="col-sm-12 text-center">
                                <div class="ribbon mb24">
                                    <h6 class="uppercase mb0">
                                        {{ $blog->created_at->translatedFormat('d F Y') }}
                                        @if($blog->category)
                                            | {{ $blog->category->title }}
                                        @endif
                                    </h6>
                                </div>
                                <h2 class="alt-font">{{ $blog->title }}</h2>
                            </div>
                        </div>

                        {{-- Blog Görseli --}}
                        @if($blog->image)
                        <div class="row mb64 mb-xs-24">
                            <div class="col-sm-10 col-sm-offset-1 text-center">
                                <img alt="{{ $blog->title }}" src="{{ asset('uploads/' . $blog->image) }}" />
                            </div>
                        </div>
                        @endif

                        {{-- Blog İçeriği --}}
                        <div class="row mb64 mb-xs-24">
                            <div class="col-sm-8 col-sm-offset-2">
                                <div class="blog-content">
                                    {!! $blog->desc !!}
                                </div>
                                </div>
                                </div>

                                {{-- Sosyal Medya Paylaşım veya Takip --}}
                                <div class="row">
                                    <div class="col-sm-12 text-center">
                                        <div class="inline-block">
                                            <h6 class="uppercase">Bizi Takip Edin</h6>
                                            <ul class="list-inline social-list">
                                                @if($settings?->facebook_url)
                                                    <li><a href="{{ $settings->facebook_url }}" target="_blank"><i class="ti-facebook"></i></a></li>
                                                @endif
                                                @if($settings?->instagram_url)
                                                    <li><a href="{{ $settings->instagram_url }}" target="_blank"><i class="ti-instagram"></i></a></li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                </div>
                                </section>
                                <hr style="margin: 0; border-color: #eee;"> {{-- Bloglar arası ince çizgi --}}
            @endforeach

                                {{-- Pagination Alanı --}}
                                <section class="pt0 pb64">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col-sm-12 text-center">
                            {{ $latestBlogs->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                </div>
            </section>

       
        @endif
    </div>
    @endif
@endsection
