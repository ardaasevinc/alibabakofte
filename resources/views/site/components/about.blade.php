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
                {{-- Resim Varsa 5-7 Düzeni, Yoksa 8-Ortalı Düzen --}}
                @if($about->image)
                    <div class="col-md-5 col-sm-6">
                        <img src="{{ Storage::disk('uploads')->url($about->image) }}" alt="{{ $about->title }}" class="img-responsive"
                            style="border-radius: 8px;">
                    </div>
                    <div class="col-md-7 col-sm-6">
                        @if($about->desc)
                            <div class="about-content">
                                {!! $about->desc !!}
                            </div>
                        @endif
                    </div>
                @elseif($about->desc)
                    <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 text-center">
                        <div class="about-content">
                            {!! $about->desc !!}
                        </div>
                    </div>
                @endif
                </div>

               
    </section>
@endif