@if($about && $about->is_published)
    <a id="about"></a>
    <section class="pb0">
        <div class="container">
            {{-- Başlık Kontrolü --}}
            @if($about->title)
                <div class="row mb64 mb-xs-40">
                    <div class="col-sm-12 text-center">
                        <div class="ribbon">
                            <h6 class="uppercase mb0">{{ $about->title }}</h6>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row mb64 mb-xs-40">
                {{-- Resim Varsa 5-7 Düzeni --}}
                @if($about->image)
                    <div class="col-md-5 col-sm-6 mb-xs-24">
                        <img src="{{ Storage::disk('uploads')->url($about->image) }}" alt="{{ $about->title }}" class="img-responsive"
                            style="border-radius: 8px; margin: 0 auto;"> {{-- Resim mobilde ortalansın diye margin: 0 auto eklendi --}}
                    </div>
                    <div class="col-md-7 col-sm-6">
                        @if($about->desc)
                            {{-- Masaüstünde ortalı (text-md-center), mobilde sola yaslı (text-left) --}}
                            <div class="about-content text-left text-md-center">
                                {!! $about->desc !!}
                            </div>
                        @endif
                    </div>
                {{-- Resim Yoksa 8-Ortalı Düzen --}}
                @elseif($about->desc)
                    <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
                         {{-- Masaüstünde ortalı, mobilde sola yaslı --}}
                        <div class="about-content text-left text-md-center">
                            {!! $about->desc !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Mobil ve Masaüstü Hizalaması İçin Küçük Bir CSS Eklemesi --}}
    <style>
        /* Bootstrap 3'te md ve üzeri için merkezi hizalama, küçüklerde sola yaslama */
        @media (min-width: 992px) {
            .text-md-center {
                text-align: center !important;
            }
            .text-md-center p, .text-md-center div {
                text-align: center !important;
            }
        }
        @media (max-width: 991px) {
            .text-left {
                text-align: left !important;
            }
            .text-left p, .text-left div {
                text-align: left !important;
            }
        }
    </style>
@endif