@if($specials && $specials->count() > 0)
    <a id="special"></a>
    @foreach($specials as $special)
        <section class="pb0 mb-40">
            <div class="container">
                <div class="row mb64 mb-xs-40">
                    <div class="col-sm-12 text-center">
                        <div class="ribbon">
                            {{-- Başlık varsa göster, yoksa varsayılan metni bas --}}
                            <h6 class="uppercase mb0">{{ $special->title ?? 'Sezon Spesiyali' }}</h6>
                        </div>
                    </div>
                </div>
                @if($special->image)
                    <div class="row mb32 mb-xs-24">
                        <div class="col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1">
                            <img alt="{{ $special->title }}" src="{{ asset('uploads/' . $special->image) }}" />
                        </div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-md-7 col-md-offset-3 col-sm-8 col-sm-offset-2">
                        @if($special->title)
                            <h2 class="alt-font">
                                {!! nl2br(e($special->title)) !!}
                            </h2>
                        @endif

                        @if($special->desc)
                            <div class="mb0">
                                {!! $special->desc !!}
                            </div>
                        @endif

                        @if($special->price)
                            <span class="block bold mt16" style="font-size: 20px;">
                                {{ number_format($special->price, 0, ',', '.') }}₺
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            </section>
    @endforeach
@endif