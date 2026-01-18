<a id="gallery"></a>
<section class="instagram-marquee-section"
    style="padding: 0; overflow: hidden; background: transparent; margin-top:50px;">
    
    @if(isset($instagramPosts) && count($instagramPosts) > 0)
        <div class="marquee-container">
            <div class="marquee-content">

                {{-- 1. SET --}}
                @foreach($instagramPosts as $post)
                    <div class="insta-card">
                        <a href="{{ $post->permalink }}" target="_blank">
                            
                            {{-- Foto/Video thumbnail seçimi --}}
                            <img 
                                src="{{ $post->media_type === 'VIDEO' 
                                        ? ($post->thumbnail_url ?? $post->media_url) 
                                        : $post->media_url }}"
                                alt="{{ $post->caption ?? 'Instagram Paylaşımı' }}"
                                loading="lazy">

                            {{-- Video ikonu --}}
                            @if($post->media_type === 'VIDEO')
                                <div class="play-icon"><i class="ti-control-play"></i></div>
                            @endif

                            {{-- Instagram hover --}}
                            <div class="insta-hover-overlay">
                                <i class="ti-instagram"></i>
                            </div>
                        </a>
                    </div>
                @endforeach

                {{-- 2. SET (sonsuz akış için) --}}
                @foreach($instagramPosts as $post)
                    <div class="insta-card">
                        <a href="{{ $post->permalink }}" target="_blank">
                            <img 
                                src="{{ $post->media_type === 'VIDEO' 
                                        ? ($post->thumbnail_url ?? $post->media_url) 
                                        : $post->media_url }}"
                                alt="{{ $post->caption ?? 'Instagram Paylaşımı' }}"
                                loading="lazy">

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
    /* Container */
    .marquee-container {
        width: 100%;
        overflow: hidden;
        position: relative;
    }

    /* Sonsuz Akış */
    .marquee-content {
        display: flex;
        gap: 1px;
        animation: marquee-flow 40s linear infinite;
        width: max-content;
    }

    .marquee-container:hover .marquee-content {
        animation-play-state: paused;
    }

    /* Kart */
    .insta-card {
        flex: 0 0 auto;
        width: 300px;
        height: 350px;
        position: relative;
        overflow: hidden;
        border-radius: 12px;
    }

    .insta-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .5s ease;
        display: block;
    }

    /* Hover Zoom */
    .insta-card:hover img {
        transform: scale(1.1);
    }

    /* Hover Overlay */
    .insta-hover-overlay {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.35);
        color: white;
        display: flex;
        align-items: center; 
        justify-content: center;
        opacity: 0;
        transition: .3s;
    }

    .insta-card:hover .insta-hover-overlay {
        opacity: 1;
    }

    .insta-hover-overlay i {
        font-size: 32px;
    }

    /* Video Icon */
    .play-icon {
        position: absolute;
        top: 15px; right: 15px;
        width: 38px; height: 38px;
        background: rgba(0,0,0,0.6);
        border-radius: 50%;
        display: flex;
        align-items: center; 
        justify-content: center;
        z-index: 2;
    }

    .play-icon i {
        color: #fff;
        font-size: 14px;
        margin-left: 2px;
    }

    /* Sonsuz Akan Keyframe */
    @keyframes marquee-flow {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    /* Mobil Uyumu */
    @media (max-width: 768px) {
        .insta-card {
            width: 180px;
            height: 220px;
        }
        .marquee-content {
            animation-duration: 25s;
        }
    }
</style>
