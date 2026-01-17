@if($videos && $videos->count() > 0)
    @foreach($videos as $video)
        {{-- Sadece video dosyası yüklü ise göster --}}
        @if($video->video_file)
            <section>
                <div class="container">
                    {{-- Başlık ve Tarih --}}
                    <div class="row mb40 mb-xs-24">
                        <div class="col-sm-12 text-center">
                            <div class="ribbon mb24">
                                <h6 class="uppercase mb0">{{ $video->created_at->translatedFormat('d F Y') }}</h6>
                            </div>
                            <h2 class="alt-font">{{ $video->title }}</h2>
                        </div>
                    </div>

                    {{-- Video Alanı --}}
                    <div class="row mb64 mb-xs-24">
                        <div class="col-sm-10 col-sm-offset-1 text-center">
                            <div class="video-wrapper">
                                <video controls 
                                       poster="{{ $video->image ? asset('uploads/' . $video->image) : '' }}" 
                                       style="width: 100%; border-radius: 12px;">
                                    <source src="{{ asset('uploads/' . $video->video_file) }}" type="video/mp4">
                                    Tarayıcınız video oynatmayı desteklemiyor.
                                </video>
                            </div>
                        </div>
                    </div>

                    {{-- Açıklama Alanı --}}
                    @if($video->desc)
                        <div class="row mb64 mb-xs-24">
                            <div class="col-sm-8 col-sm-offset-2 text-center">
                                <div class="lead">{!! $video->desc !!}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </section>
            <hr style="margin:0; opacity: 0.1;">
        @endif
    @endforeach
@endif