@extends('site.layouts.site')

@section('content')
<div class="main-container">
    <section>
        <div class="container">
            {{-- Üst Alan: Kategori, Tarih ve Başlık --}}
            <div class="row mb40 mb-xs-24">
                <div class="col-sm-12 text-center">
                    <div class="ribbon mb24">
                        <h6 class="uppercase mb0">
                            @if($blog->category)
                                {{ $blog->category->title }}
                            @endif
                        </h6>
                        <span class="mb24">{{ $blog->created_at->translatedFormat('d F Y') }}</span>
                    </div>
                    <h1 class="alt-font">{{ $blog->title }}</h1>
                </div>
            </div>

            {{-- Blog Ana Görseli --}}
            @if($blog->image)
            <div class="row mb64 mb-xs-24">
                <div class="col-sm-10 col-sm-offset-1 text-center">
                    <img alt="{{ $blog->title }}" src="{{ asset('uploads/' . $blog->image) }}" class="img-responsive" style="display: inline-block; height:600px;" />
                </div>
            </div>
            @endif

            {{-- Blog İçeriği ve Etiketler --}}
            <div class="row mb64 mb-xs-24">
                <div class="col-sm-8 col-sm-offset-2">
                    <div class="blog-content">
                        {!! $blog->desc !!}
                    </div>

                    {{-- Etiketler (Tags) --}}
                    @if($blog->tags && count($blog->tags) > 0)
                        <div class="mt40">
                            <hr>
                            <p><strong>Etiketler:</strong></p>
                            <ul class="list-inline social-list">
                                @foreach($blog->tags as $tag)
                                    <li>
                                        <span class="label label-default" style="font-weight: normal; padding: 5px 10px;">#{{ $tag }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sosyal Medya Paylaşım (Boot'tan gelen settings kullanılır) --}}
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
</div>
@endsection