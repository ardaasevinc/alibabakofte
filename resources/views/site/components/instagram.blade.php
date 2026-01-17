<a id="gallery"></a>
<section class="instagram-marquee-section"
    style="padding: 0; overflow: hidden; background: transparent; margin-top:50px;">
    
    {{-- Ana Taşıyıcı: Mouse üzerine gelince akış durur (isteğe bağlı) --}}
    <div class="marquee-container" onmouseover="this.style.animationPlayState='paused'" onmouseout="this.style.animationPlayState='running'">
        <div class="marquee-content">
            {{-- İlk set resimler --}}
            @foreach($instagramPosts as $post)
                <div class="insta-card">
                    <a href="{{ $post->permalink }}" target="_blank">
                        <img src="{{ $post->media_url }}" alt="{{ $post->caption }}">
                        @if($post->media_type === 'VIDEO')
                            <div class="play-icon"><i class="ti-control-play"></i></div>
                        @endif
                    </a>
                </div>
            @endforeach

            {{-- Kesintisiz döngü için aynı resimleri bir kez daha basıyoruz --}}
            @foreach($instagramPosts as $post)
                <div class="insta-card">
                    <a href="{{ $post->permalink }}" target="_blank">
                        <img src="{{ $post->media_url }}" alt="{{ $post->caption }}">
                        @if($post->media_type === 'VIDEO')
                            <div class="play-icon"><i class="ti-control-play"></i></div>
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<style>
    /* Marquee (Film Şeridi) Ayarları */
    .marquee-container {
        width: 100%;
        overflow: hidden;
        white-space: nowrap;
        position: relative;
    }

    .marquee-content {
        display: flex;
        gap: 1px; /* İstediğin 1px boşluk */
        animation: marquee-flow 40s linear infinite; /* Hızı buradan ayarlayabilirsin (40s daha yavaştır) */
        width: max-content;
    }

    .insta-card {
        flex: 0 0 auto;
        width: 300px; /* Görsel genişliği */
        height: 350px; /* Görsel yüksekliği */
        position: relative;
    }

    .insta-card img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* 1080x1350 olsa da alanı tam doldurur */
        display: block;
    }

    /* Video Play İkonu */
    .play-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 50px;
        height: 50px;
        background: rgba(255,255,255,0.8);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }
    
    .play-icon i {
        color: #000;
        font-size: 20px;
        margin-left: 3px;
    }

    /* Akış Animasyonu */
    @keyframes marquee-flow {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); } /* İçerik iki katı olduğu için tam yarısında başa sarar */
    }

    /* Mobil için yükseklik ayarı */
    @media (max-width: 768px) {
        .insta-card { width: 200px; height: 250px; }
    }
</style>