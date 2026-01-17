@if(isset($banner) && (is_array($banner) || $banner instanceof \Illuminate\Support\Collection) && count($banner) > 0)
    <section class="pb0 pt0">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 text-center">
                    {{-- data-animation="fade" yumuşak geçişi sağlar --}}
                    <div class="image-slider slider-arrow-controls mb0" data-animation="fade" data-speed="5000">
                        <ul class="slides">
                            @foreach($banner as $item)
                                @if(!empty($item->image))
                                    <li class="v-align-transform">
                                        <img alt="{{ $item->title ?? 'Ali Baba Köfte' }}" 
                                             src="{{ asset('uploads/' . $item->image) }}" />
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@else
    {{-- Banner yoksa veya hata varsa boş kalmasın diye istersen buraya sabit bir görsel koyabilirsin --}}
@endif

<script>
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof $.flexslider === "function") {
            $('.image-slider').flexslider({
                animation: "fade",      
                slideshowSpeed: 5000,   
                animationSpeed: 1000,   
                controlNav: false,      
                directionNav: true      
            });
        }
    });
</script>