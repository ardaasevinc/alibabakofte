@if(isset($banner) && is_iterable($banner) && count($banner) > 0)
    <section class="pb0 pt0">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 text-center">
                    {{-- data-animation="fade" ve smoothHeight:false zıplamayı engeller --}}
                    <div class="image-slider slider-arrow-controls mb0" data-animation="fade">
                        <ul class="slides" style="background: #f8f8f8;">
                            @foreach($banner as $item)
                                @if(!empty($item->image))
                                    <li>
                                        <div class="banner-wrapper" style="height: 500px; overflow: hidden; width: 100%;">
                                            <img alt="{{ $item->title ?? 'Ali Baba Köfte' }}" 
                                                 src="{{ asset('uploads/' . $item->image) }}" 
                                                 style="width: 100%; height: 100%; object-fit: cover;" />
                                        </div>
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
    /* Slider'ın ilk açılışta zıplamasını engellemek için */
    .image-slider .slides, 
    .image-slider .slides li, 
    .image-slider .banner-wrapper {
        height: 500px !important;
    }

    /* Mobilde yüksekliği otomatik olarak daraltalım */
    @media (max-width: 768px) {
        .image-slider .slides, 
        .image-slider .slides li, 
        .image-slider .banner-wrapper {
            height: 300px !important;
        }
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof $.flexslider === "function") {
            $('.image-slider').flexslider({
                animation: "fade",      // Fade in - Fade out efekti
                slideshowSpeed: 5000,   // Resmin kalma süresi
                animationSpeed: 1000,   // Geçiş hızı
                smoothHeight: false,    // Yüksekliğin resme göre değişmesini kapatır (zıplama olmaz)
                controlNav: false,
                directionNav: true,
                start: function(slider) {
                    slider.removeClass('loading');
                }
            });
        }
    });
</script>