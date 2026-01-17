@if($videos && $videos->count() > 0)
    @foreach($videos as $video)
        <section>
            <div class="container">
                <div class="row mb40 mb-xs-24">
                    <div class="col-sm-12 text-center">
                        <div class="ribbon mb24">
                            <h6 class="uppercase mb0">{{ $video->created_at->translatedFormat('d F Y') }}</h6>
                        </div>
                        <h2 class="alt-font">{{ $video->title }}</h2>
                    </div>
                </div>

                <div class="row mb64 mb-xs-24">
                    <div class="col-sm-10 col-sm-offset-1 text-center">
                        <div class="video-wrapper">
                            {{-- 1. DURUM: YouTube/Vimeo Linki (video_url) --}}
                            @if($video->video_url)
                                @php
                                    // YouTube linkini otomatik olarak embed formatına çeviriyoruz
                                    $url = $video->video_url;
                                    $embedUrl = $url;

                                    if (str_contains($url, 'youtube.com/watch?v=')) {
                                        $embedUrl = str_replace('watch?v=', 'embed/', $url);
                                    } elseif (str_contains($url, 'youtu.be/')) {
                                        $parts = explode('/', $url);
                                        $embedUrl = 'https://www.youtube.com/embed/' . end($parts);
                                    }
                                    
                                    // Varsa gereksiz parametreleri temizle (örn: &t=10s)
                                    $embedUrl = explode('&', $embedUrl)[0];
                                @endphp
                                <iframe src="{{ $embedUrl }}" title="{{ $video->title }}"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                                </iframe>

                            {{-- 2. DURUM: Yüklenmiş Dosya (video_file) --}}
                            @elseif($video->video_file)
                                <video controls>
                                    <source src="{{ asset('uploads/' . $video->video_file) }}" type="video/mp4">
                                    Tarayıcınız video oynatmayı desteklemiyor.
                                </video>
                            @endif
                        </div>
                    </div>
                </div>

                @if($video->desc)
                    <div class="row mb64 mb-xs-24">
                        <div class="col-sm-8 col-sm-offset-2 text-center">
                            <div class="lead">{!! $video->desc !!}</div>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endforeach
@endif