@if($videos && $videos->count() > 0)
    @foreach($videos as $video)
        <section>
            <div class="container">
                {{-- Başlık ve Tarih Alanı --}}
                <div class="row mb40 mb-xs-24">
                    <div class="col-sm-12 text-center">
                        <div class="ribbon mb24">
                            <h6 class="uppercase mb0">{{ $video->created_at->translatedFormat('d F Y') }}</h6>
                        </div>
                        @if($video->title)
                            <h2 class="alt-font">{{ $video->title }}</h2>
                        @endif
                    </div>
                </div>

                <style>
                    .video-wrapper {
                        position: relative;
                        padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
                        height: 0;
                        overflow: hidden;
                        max-width: 100%;
                        border-radius: 12px;
                        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                    }
                    .video-wrapper iframe, .video-wrapper video {
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        border: 0;
                    }
                </style>

                <div class="row mb64 mb-xs-24">
                    <div class="col-sm-10 col-sm-offset-1 text-center">
                        <div class="video-wrapper">
                            {{-- Durum 1: Harici Link (YouTube/Vimeo) --}}
                            @if($video->link)
                                @php
                                    // YouTube linkini embed formatına çevirme (isteğe bağlı, panelden direkt embed de girilebilir)
                                    $embedUrl = str_replace(['watch?v=', 'youtu.be/'], ['embed/', 'youtube.com/embed/'], $video->link);
                                @endphp
                                <iframe src="{{ $embedUrl }}" title="{{ $video->title }}"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                    allowfullscreen>
                                </iframe>
                            
                    {{--  --}}ası --}}
                            @elseif($video->video_file)
                                <video controls poster="{{ $video->image ? asset('uploads/' . $video->image) : '' }}">
                                    <source src="{{ asset('uploads/' . $video->video_file) }}" type="video/mp4">
                                    Tarayıcınız video oynatmayı desteklemiyor.
                                </video>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Açıklama Alanı --}}
                @if($video->desc)
                    <div class="row mb64 mb-xs-24">
                        <div class="col-sm-8 col-sm-offset-2 text-center">
                            <div class="lead">
                                {!! $video->desc !!}
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Sosyal Medya ve Takip Alanı --}}
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <div class="inline-block">
                            <h6 class="uppercase">DAHA FAZLASI İÇİN BİZİ TAKİP EDİN</h6>
                            <ul class="list-inline social-list">
                                @if($settings?->map_link)
                                    <li>
                                        <a href="{{ $settings->map_link }}" target="_blank">
                                            <span>Google'da yorum yap</span>
                                        </a>
                                    </li>
                                @endif
                                @if($settings?->facebook_url)
                                    <li>
                                        <a href="{{ $settings->facebook_url }}" target="_blank">
                                            <i class="ti-facebook"></i>
                                        </a>
                                    </li>
                                @endif
                                @if($settings?->instagram_url)
                                    <li>
                                        <a href="{{ $settings->instagram_url }}" target="_blank">
                                            <i class="ti-instagram"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <hr style="margin:0; opacity: 0.1;">
    @endforeach
@endif