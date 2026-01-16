@if($settings)
    <a id="contact"></a>
    <section>
        <div class="container">
            <div class="row mb64 mb-xs-40">
                <div class="col-sm-12 text-center">
                    <div class="ribbon">
                        <h6 class="uppercase mb0">BİZE ULAŞIN</h6>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="map-holder pt120 pb120" style="position: relative;">
                        {{-- Harita Iframe Kontrolü --}}
                        @if($settings->map_iframe)
                            <div class="map-container"
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; overflow: hidden;">
                                {!! $settings->map_iframe !!}
                            </div>
                        @endif

                        <div class="col-md-6 col-md-offset-3 col-sm-10 col-sm-offset-1 contact-details" style="position: relative; z-index: 1;">
                            <div class="pt80 pb56 bg-white p32 overflow-hidden shadow-lg"
                                style="box-shadow: 0 15px 35px rgba(0,0,0,0.1); border-radius: 8px;">
                                <div class="col-sm-10 col-sm-offset-1 text-center">
                                    
                                    @if($settings->work_time)
                                        <div class="mb16">
                                        Çalışma Saatleri: <br>
                                            {!! $settings->work_time !!}
                                        </div>
                                        <hr>
                                    @endif

                                    {{-- Adres Bilgileri --}}
                                    <p>
                                        <strong>{{ $settings->meta_title ?? 'Çatalcalı Ali Baba Köfte Salonu' }}</strong>
                                        @if($settings->address)
                                            <br>{!! nl2br(e($settings->address)) !!}
                                        @endif
                                    </p>

                                    {{-- İletişim Kanalları --}}
                                    <p>
                                        @if($settings->phone)
                                            <strong>Telefon:</strong> <a href="tel:{{ $settings->phone }}">{{ $settings->phone }}</a>
                                        @endif

                                        @if($settings->email)
                                            <br> <strong>E-posta:</strong> <a href="mailto:{{ $settings->email }}">{{ $settings->email }}</a>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        </div>

                        {{-- Yol Tarifi Butonu --}}
                        @if($settings->map_link)
                            <div class="text-center" style="position: relative; z-index: 1; margin-top:-175px;">
                                <a href="{{ $settings->map_link }}" target="_blank" class="btn btn-sm btn-filled">Yol Tarifi Al</a>
                            </div>
                        @endif
                </div>
            </div>
        </div>
    </section>
@endif