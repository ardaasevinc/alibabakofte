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
                        <ul class="list-inline mb0">
                            <li class="mb8">
                                <a style="color:white !important; font-size: 11px; letter-spacing: 1px; text-transform: uppercase;"
                                    href="{{ route('site.permission.kvkk') }}" class="fade-half hover-white">
                                    KVKK Aydınlatma Metni
                                </a>
                                </li>
                            <li style="color: rgba(255,255,255,0.3);">|</li> {{-- Aradaki ayraç --}}
                            <li>
                                <a style="color:white !important; font-size: 11px; letter-spacing: 1px; text-transform: uppercase;"
                                    href="{{ route('site.permission.acikriza') }}" class="fade-half hover-white">
                                    Açık Rıza Beyanı
                                </a>
                                </li>
                                </ul>
                                </div>

                    <style>
                        .legal-links .list-inline li {
                            display: inline-block;
                            padding: 0 5px;
                        }

                        .legal-links .fade-half {
                            opacity: 0.6;
                            transition: opacity 0.3s ease;
                        }

                        .legal-links .fade-half:hover {
                            opacity: 1;
                        }
                    </style>

                </div>
            </div>
            </div>
            </footer>
@endif