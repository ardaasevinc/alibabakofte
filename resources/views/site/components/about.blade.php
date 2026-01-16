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

                {{-- Sabit Alt Alanlar --}}
                <div class="row mb64 mb-xs-24">
                    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 text-center">
                        <p class="alt-font mb8">
                            "1997'den beri Çatalca'da değişmeyen tek adres. Ali Baba'nın o meşhur özel soslu köftesi gerçek bir klasik."
                        </p>
                        <span class="sub">Müşteri Yorumu - Google</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 text-center">
                        <p class="alt-font mb8">
                            "İki kuşaktır süregelen bu aile geleneği, Çatalca'nın yerel lezzetlerini en doğal haliyle soframıza
                            taşıyor."
                        </p>
                        <span class="sub">Gurme Rehberi</span>
                    </div>
                </div>
                </div>
    </section>
@endif