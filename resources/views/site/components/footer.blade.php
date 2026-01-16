@if($settings)
<footer class="bg-primary pt120 pb120 pt-xs-40 pb-xs-64" style="margin-top:100px;">
    <div class="container">
        <div class="row">
            
            <div class="col-sm-6 mb-xs-24 text-center-xs">
                {{-- Logo Alanı --}}
                @if($settings->logo_light)
                <div class="inline-block center">
                    <img alt="{{ $settings->meta_title ?? 'Ali Baba Köfte' }}" 
                         class="image-small display-block center mb-xs-24" 
                         src="{{ asset('uploads/' . $settings->logo_light) }}" />
                </div>
                @endif

                <div class="inline-block display-block-xs text-center-xs">
                    <h6 class="uppercase mb8">{{ $settings->meta_title ?? 'Ali Baba Köfte' }}</h6>
                    
                    @if($settings->slogan)
                        <span class="sub display-block">{{ $settings->slogan }}</span>
                    @endif

                    @if($settings->address)
                        <span class="sub display-block">{{ $settings->address }}</span>
                    @endif

                    @if($settings->work_time)
                        {{-- Alt kısımda RichEditor verisini sade metin olarak göstermek isterseniz strip_tags kullanabilirsiniz --}}
                        <span class="sub display-block">
                            Çalışma Saatleri: {!! strip_tags($settings->work_time) !!}
                        </span>
                    @endif
                </div>
            </div>

            <div class="col-md-3 col-md-offset-3 col-sm-4 col-sm-offset-2 text-center">
                <h6 class="uppercase">Bizi Takip Edin</h6>
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
        </div>
        </div>
    </footer>
@endif