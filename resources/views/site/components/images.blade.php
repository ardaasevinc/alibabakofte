@if($galleries && $galleries->count() > 0)
    <section class="pb0 pt0 overflow-hidden">
        <div class="container-fluid p0">
            <div class="row">
                <div class="col-sm-12">
                    <div class="horizontal-scroll-gallery">
                        <div class="gallery-container">
                            @foreach($galleries as $index => $item)
                                {{-- Her 3 resimde bir geniş kart (card-wide) yaparak ritim oluşturuyoruz --}}
                                <div class="gallery-card {{ ($index % 3 == 1) ? 'card-wide' : 'card-narrow' }}">
                                    @if($item->image)
                                        <img alt="{{ $item->title ?? 'Ali Baba Köfte Galeri' }}" src="{{ asset('uploads/' . $item->image) }}" />

                                        @if($item->title)
                                            <div class="card-overlay">
                                                <span>{{ $item->title }}</span>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                            </div>
                            </div>
                            </div>
                            </div>
                            </div>
                            </section>

                            <style>
                                /* Orijinal CSS yapınızı buraya aynen ekleyebilirsiniz, hiçbir çakışma olmaz */
                                .horizontal-scroll-gallery {
                                    width: 100%;
                                    overflow-x: auto;
                                    overflow-y: hidden;
                                    -webkit-overflow-scrolling: touch;
                                    scrollbar-width: none;
                                    padding: 60px 0;
                                }

                                .horizontal-scroll-gallery::-webkit-scrollbar {
                                    display: none;
                                }

                                .gallery-container {
                                    display: flex;
                                    padding-left: 8%;
                                }

                                .gallery-card {
                                    flex: 0 0 auto;
                                    margin-right: 40px;
                                    overflow: hidden;
                                    position: relative;
                                    box-shadow: 0 15px 45px rgba(0, 0, 0, 0.15);
                                    transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
                                }

                                .gallery-card img {
                                    height: 550px;
                                    width: auto;
                                    display: block;
                                    object-fit: cover;
                                    filter: grayscale(100%);
                                    -webkit-filter: grayscale(100%);
                                    transition: filter 0.6s ease, transform 0.6s ease;
                                }

                                .card-narrow img {
                                    width: 400px;
                                }

                                .card-wide img {
                                    width: 800px;
                                }

                                .gallery-card:hover img {
                                    filter: grayscale(0%);
                                    -webkit-filter: grayscale(0%);
                                    transform: scale(1.05);
                                }

                                .card-overlay {
                                    position: absolute;
                                    bottom: 0;
                                    left: 0;
                                    width: 100%;
                                    padding: 40px 20px;
                                    background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
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

                                .gallery-card:hover .card-overlay {
                                    opacity: 1;
                                    transform: translateY(0);
                                }

                                @media (max-width: 768px) {
                                    .gallery-container {
                                        padding-left: 20px;
                                    }

                                    .gallery-card {
                                        margin-right: 20px;
                                    }

                                    .gallery-card img {
                                        height: 380px;
                                    }

                                    .card-narrow img {
                                        width: 280px;
                                    }

                                    .card-wide img {
                                        width: 500px;
                                    }
        }
    </style>
@endif