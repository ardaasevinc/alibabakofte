@if($settings)
    <footer class="bg-primary pt120 pb120 pt-xs-40 pb-xs-64" style="margin-top:100px;">
        <div class="container">
            <div class="row">
                {{-- Tüm İçeriği Kapsayan ve Ortaya Hizalayan Sütun --}}
                <div class="col-sm-12 text-center">

                    {{-- Logo Alanı --}}
                    @if($settings->logo_light)
                        <div class="mb24">
                            <img alt="{{ $settings->meta_title ?? 'Ali Baba Köfte' }}" class="image-small inline-block"
                                src="{{ asset('uploads/' . $settings->logo_light) }}" />
                        </div>
                    @endif

                    {{-- Metin Bilgileri --}}
                    <div class="mb32">
                        <h6 class="uppercase mb8">{{ $settings->meta_title ?? 'Ali Baba Köfte' }}</h6>

                        @if($settings->slogan)
                            <span class="sub display-block">{{ $settings->slogan }}</span>
                        @endif

                        @if($settings->address)
                            <span class="sub display-block">{{ $settings->address }}</span>
                        @endif

                        @if($settings->work_time)
                            <span class="sub display-block">
                                Çalışma Saatleri: {!! strip_tags($settings->work_time) !!}
                            </span>
                        @endif
                    </div>

                    {{-- Sosyal Medya --}}
                    <div>
                        <h6 class="uppercase mb16">Bizi Takip Edin</h6>
                        <ul class="list-inline social-list">
                            @if($settings->facebook_url)
                                <li>
                                    <a href="{{ $settings->facebook_url }}" target="_blank">
                                        <i class="ti-facebook"></i>
                                    </a>
                                </li>
                            @endif

                            @if($settings->instagram_url)
                                <li>
                                    <a href="{{ $settings->instagram_url }}" target="_blank">
                                        <i class="ti-instagram"></i>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>

                   <div class="legal-links">
        <ul class="list-unstyled">
            <li class="mb8">
                <a href="{{ route('site.permission.kvkk') }}" class="fade-half hover-white" style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase;">
                    KVKK Aydınlatma Metni
                </a>
            </li>
            <li>
                <a href="{{ route('site.permission.acikriza') }}" class="fade-half hover-white" style="font-size: 11px; letter-spacing: 1px; text-transform: uppercase;">
                    Açık Rıza Beyanı
                </a>
            </li>
        </ul>
    </div>

                </div>
                </div>
                </div>
                </footer>
@endif