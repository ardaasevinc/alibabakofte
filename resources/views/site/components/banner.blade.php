@if(isset($banner) && count($banner) > 0)
    <section class="pb0 pt0">
        <div class="container-fluid p0"> {{-- Kenar boşluklarını sıfırlamak için fluid ve p0 ekledik --}}
            <div class="row">
                <div class="col-sm-12 text-center">
                    <div class="image-slider slider-arrow-controls mb0" data-animation="fade" data-speed="5000">
                        <ul class="slides" style="height: 500px; overflow: hidden;"> {{-- Sabit yükseklik alanı --}}
                            @foreach($banner as $item)
                                @if(!empty($item->image))
                                    <li class="v-align-transform">
                                        <img alt="{{ $item->title ?? 'Ali Baba Köfte' }}" 
                                             src="{{ asset('uploads/' . $item->image) }}" 
                                             style="height: 800px; object-fit: cover;" />
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

<style>
    /* Slider yüksekliğinin mobilde daha küçük, desktopta sabit kalması için */
    .image-slider .slides li {
        height: 500px;
    }
    
    @media (max-width: 768px) {
        .image-slider .slides, .image-slider .slides li, .image-slider .slides li img {
            height: 300px !important; /* Mobilde yükseklik düşer */
        }
    }

    /* Flexslider'ın garip zıplamalarını engeller */
    .flex-viewport {
        max-height: 500px;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof $.flexslider === "function") {
            $('.image-slider').flexslider({
                animation: "fade",
                slideshowSpeed: 5000,
                animationSpeed: 1000,
                controlNav: false,
                directionNav: true,
                smoothHeight: false, // Yüksekliğin her resme göre değişmesini engeller
                start: function(slider){
                    $('.image-slider').removeClass('loading');
                }
            });
        }
    });
</script>