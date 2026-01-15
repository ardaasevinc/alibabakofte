<section class="pb0 pt0 overflow-hidden">
    <div class="container-fluid p0">
        <div class="row">
            <div class="col-sm-12">
                <div class="horizontal-scroll-gallery">
                    <div class="gallery-container">
                        <div class="gallery-card card-narrow">
                            <img alt="Ali Baba Köfte İç Mekan" src="{{ asset('site/img/body1.jpg') }}" />
                            <div class="card-overlay">
                                <span>1997'den Beri Aynı Lezzet</span>
                            </div>
                        </div>
                        <div class="gallery-card card-wide">
                            <img alt="Ali Baba Köfte Sunum" src="{{ asset('site/img/body2.jpg') }}" />
                            <div class="card-overlay">
                                <span>Geleneksel Çatalca Köftesi</span>
                            </div>
                        </div>
                        <div class="gallery-card card-narrow">
                            <img alt="Ali Baba Köfte Detay" src="{{ asset('site/img/body2.jpg') }}" />
                            <div class="card-overlay">
                                <span>Ali Soy'un Mirası</span>
                            </div>
                        </div>
                        <div class="gallery-card card-narrow">
                            <img alt="Ali Baba Köfte Detay" src="{{ asset('site/img/body2.jpg') }}" />
                            <div class="card-overlay">
                                <span>Ali Soy'un Mirası</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Galeri Ana Kapsayıcı */
    .horizontal-scroll-gallery {
        width: 100%;
        overflow-x: auto;
        overflow-y: hidden;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none; /* Firefox */
        padding: 60px 0;
    }

    .horizontal-scroll-gallery::-webkit-scrollbar {
        display: none; /* Chrome, Safari */
    }

    .gallery-container {
        display: flex;
        padding-left: 8%; /* İlk resmin başlangıç payı */
    }

    /* Kart Yapısı */
    .gallery-card {
        flex: 0 0 auto;
        margin-right: 40px;
        border-radius: 15px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 15px 45px rgba(0,0,0,0.15);
        transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    /* Görsel Ayarları ve Grayscale Efekti */
    .gallery-card img {
        height: 550px;
        width: auto;
        display: block;
        object-fit: cover;
        filter: grayscale(100%);
        -webkit-filter: grayscale(100%);
        transition: filter 0.6s ease, transform 0.6s ease;
    }

    /* Boyut Sınıfları */
    .card-narrow img { width: 400px; }
    .card-wide img { width: 800px; }

    /* Hover Durumu: Renklenme ve Büyüme */
    .gallery-card:hover img {
        filter: grayscale(0%);
        -webkit-filter: grayscale(0%);
        transform: scale(1.05);
    }

    /* Yazı Katmanı (Overlay) */
    .card-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 40px 20px;
        background: linear-gradient(transparent, rgba(0,0,0,0.7));
        color: #fff;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.4s ease;
        text-align: center;
        pointer-events: none;
    }

    .card-overlay span {
        font-family: inherit;
        text-transform: uppercase;
        letter-spacing: 2px;
        font-weight: 600;
        font-size: 14px;
    }

    /* Hover Durumu: Yazının Görünmesi */
    .gallery-card:hover .card-overlay {
        opacity: 1;
        transform: translateY(0);
    }

    /* Mobil Uyumluluk */
    @media (max-width: 768px) {
        .gallery-container { padding-left: 20px; }
        .gallery-card { margin-right: 20px; }
        .gallery-card img { height: 380px; }
        .card-narrow img { width: 280px; }
        .card-wide img { width: 500px; }
        
        /* Mobilde grayscale'i kapatıp direkt renkli de bırakabilirsiniz 
           veya dokunmatik (hover) etkisini koruyabilirsiniz */
    }
</style>