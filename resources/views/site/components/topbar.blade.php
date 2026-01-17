<div class="container">
    <div class="row">
        {{-- Sol Taraf: Adres (Sadece Şehir/Semt Kısmı veya Kısa Adres) --}}
        <div class="col-sm-4 text-center-xs">
            <span class="sub">
                <a href="{{ $settings?->map_link }}">{{ $settings?->address ? Str::limit($settings->address, 30) : 'Çatalca, İstanbul' }}</a>
            </span>
        </div>

        {{-- Orta Taraf: Sosyal Medya ve Google Yorum --}}
        <div class="col-sm-4 text-center">
            <ul class="list-inline social-list">
                {{-- Google Yorum Yap Linki --}}
                @if($settings?->gpage_comment)
                    <li>
                        <a href="{{ $settings->gpage_comment }}" style="color:white;" target="_blank">
                            <span class="sub">Google'da Yorum yap</span>
                        </a>
                    </li>
                @endif

                {{-- Facebook --}}
                @if($settings?->facebook_url)
                    <li>
                        <a href="{{ $settings->facebook_url }}" target="_blank">
                            <i class="ti-facebook"></i>
                        </a>
                    </li>
                @endif

                {{-- Instagram --}}
                @if($settings?->instagram_url)
                    <li>
                        <a href="{{ $settings->instagram_url }}" target="_blank">
                            <i class="ti-instagram"></i>
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        {{-- Sağ Taraf: Çalışma Saatleri --}}
        <div class="col-sm-4 text-right text-center-xs">
            <span class="sub">
                Çalışma Saatleri: {!! $settings?->work_time ? strip_tags($settings->work_time) : '05:30 - 20:00' !!}
            </span>
        </div>
    </div>
    </div>