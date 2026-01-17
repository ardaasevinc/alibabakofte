@if($banners && $banners->count() > 0)
<section class="pb0 pt0">
    <div class="container">
        <div class="row">
            <div class="col-sm-12 text-center">
                {{-- data-animation="fade" ile geçiş efektini belirliyoruz --}}
                <div class="image-slider slider-arrow-controls mb0" data-animation="fade" data-speed="3000">
                    <ul class="slides">
                        @foreach($banners as $banner)
                            <li>
                                <img alt="{{ $banner->title ?? 'Ali Baba Köfte Banner' }}" 
                                     src="{{ asset('uploads/' . $banner->image) }}" />
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- Eğer temanın flexslider'ı otomatik tetiklemiyorsa bu küçük script'i ekleyebilirsin --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof $.flexslider === "function") {
            $('.image-slider').flexslider({
                animation: "fade", // Kayma yerine solma efekti
                slideshowSpeed: 5000, // 5 saniyede bir değişsin
                animationSpeed: 1000, // Geçiş süresi 1 saniye
                controlNav: false, // Alt noktaları gizle (isteğe bağlı)
                directionNav: true // Yan okları göster
            });
        }
    });
</script>