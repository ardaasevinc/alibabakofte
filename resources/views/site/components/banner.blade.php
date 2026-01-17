@if($banner && $banner->count() > 0)
    <section class="pb0 pt0">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 text-center">
                    {{-- data-animation="fade" yumuşak geçişi, data-speed ise süreyi belirler --}}
                    <div class="image-slider slider-arrow-controls mb0" data-animation="fade" data-speed="5000">
                        <ul class="slides">
                            @foreach($banner as $item)
                                <li class="v-align-transform">
                                    <img alt="{{ $item->title ?? 'Ali Baba Köfte' }}"
                                        src="{{ asset('uploads/' . $item?->image) }}" />
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif

{{-- Temadaki slider'ın fade (solma) modunda çalışması için manuel tetikleme --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof $.flexslider === "function") {
            $('.image-slider').flexslider({
                animation: "fade",      // Kayma yerine solma efekti
                slideshowSpeed: 5000,   // Görselin ekranda kalma süresi (5 sn)
                animationSpeed: 1000,   // Geçiş sırasındaki solma hızı (1 sn)
                controlNav: false,      // Alt noktaları gizle
                directionNav: true      // Sağ-sol oklarını göster
            });
        }
    });
</script>