@if($specials && $specials->count() > 0)
    <a id="special"></a>
    @foreach($specials as $special)
        <section class="pb0">
            <div class="container">
                <div class="row mb64 mb-xs-40">
                    <div class="col-sm-12 text-center">
                        <div class="ribbon">
                            <h6 class="uppercase mb0">{{ $special->title ?? 'Sezon Spesiyali' }}</h6>
                        </div>
                    </div>
                </div>
                
                {{-- Görsel Alanı: col-md-offset-2 ile merkeze çekildi --}}
                @if($special->image)
                    <div class="row mb32 mb-xs-24">
                        <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 text-center">
                            <img alt="{{ $special->title }}" src="{{ asset('uploads/' . $special->image) }}" class="img-responsive" style="display: inline-block; border-radius: 8px;" />
                        </div>
                    </div>
                @endif

                <div class="row">
                    {{-- Metin Alanı: col-md-offset-2.5 (veya benzeri) mantığıyla merkeze hizalandı --}}
                    <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
                        @if($special->desc)
                            <div class="special-content text-left text-md-center">
                                {!! $special->desc !!}
                            </div>
                        @endif

                        @if($special->price)
                            <div class="text-left text-md-center">
                                <span class="block bold mt16" style="font-size: 24px; color: #333;">
                                    {{ number_format($special->price, 0, ',', '.') }}₺
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endforeach

    {{-- Responsive Hizalama İçin Style Bloğu --}}
    <style>
        @media (min-width: 992px) {
            .text-md-center {
                text-align: center !important;
            }
            .text-md-center * {
                text-align: center !important;
            }
        }
        @media (max-width: 991px) {
            .text-left {
                text-align: left !important;
            }
            .text-left * {
                text-align: left !important;
            }
        }
    </style>
@endif