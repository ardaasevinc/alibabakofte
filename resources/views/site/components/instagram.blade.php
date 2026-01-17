<a id="gallery"></a>
<section class="instagram-marquee-section"
    style="padding: 0; overflow: hidden; background: transparent; margin-top:50px;">
    
    @if(isset($instagramPosts) && count($instagramPosts) > 0)
        <div class="marquee-container" onmouseover="this.style.animationPlayState='paused'"
            onmouseout="this.style.animationPlayState='running'">
            <div class="marquee-content">
                {{-- Birinci Set --}}
                @foreach($instagramPosts as $post)
                    <div class="insta-card">
                        <a href="{{ $post->permalink }}" target="_blank">
                            {{-- Resim yoksa thumbnail, o da yoksa media_url (Video desteği için) --}}
                            <img src="{{ $post->media_type === 'VIDEO' ? ($post->thumbnail_url ?? $post->media_url) : $post->media_url }}"
                                alt="{{ $post->caption ?? 'Ali Baba Köfte Instagram' }}" loading="lazy">

                            @if($post->media_type === 'VIDEO')
                                <div class="play-icon"><i class="ti-control-play"></i></div>
                            @endif
                            <div class="insta-hover-overlay">
                                <i class="ti-instagram"></i>
                            </div>
                        </a>
                    </div>
                @endforeach

                {{-- Kesintisiz döngü için İkinci Set --}}
                @foreach($instagramPosts as $post)
                    <div class="insta-card">
                        <a href="{{ $post->permalink }}" target="_blank">
                            <img src="{{ $post->media_type === 'VIDEO' ? ($post->thumbnail_url ?? $post->media_url) : $post->media_url }}"
                                alt="{{ $post->caption ?? 'Ali Baba Köfte Instagram' }}" loading="lazy">
                            @if($post->media_type === 'VIDEO')
                                <div class="play-icon"><i class="ti-control-play"></i></div>
                            @endif
                            <div class="insta-hover-overlay">
                                <i class="ti-instagram"></i>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>

<style>
    .marquee-container { width: 100%; overflow: hidden; white-space: nowrap; position: relative; cursor: pointer; }
    .marquee-content { display: flex; gap: 1px; animation: marquee-flow 40s linear infinite; width: max-content; }
    .insta-card { flex: 0 0 auto; width: 300px; height: 350px; position: relative; overflow: hidden; }
    .insta-card img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform 0.5s ease; }
    
    /* Hover Efekti: Resim hafif büyüsün ve ikon gelsin */
    .insta-card:hover img { transform: scale(1.1); }
    .insta-hover-overlay {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.3); color: white; display: flex;
        align-items: center; justify-content: center; opacity: 0; transition: 0.3s;
    }
    .insta-card:hover .insta-hover-overlay { opacity: 1; }
    .insta-hover-overlay i { font-size: 30px; }

    .play-icon {
        position: absolute; top: 20px; right: 20px; /* Ortada durması yerine köşede durması görselleri daha iyi gösterir */
        width: 35px; height: 35px; background: rgba(0,0,0,0.6); border-radius: 50%;
        display: flex; align-items: center; justify-content: center; z-index: 2;
    }
    .play-icon i { color: #fff; font-size: 14px; margin-left: 2px; }

    @keyframes marquee-flow { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

    @media (max-width: 768px) {
        .insta-card { width: 180px; height: 220px; }
        .marquee-content { animation: marquee-flow 25s linear infinite; } /* Mobilde mesafe kısa olduğu için hızı artırdık */
    }
</style>