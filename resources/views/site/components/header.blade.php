<div class="nav-container">
    <a id="top"></a>
    <nav class="pb16 pb-xs-0">
        <div class="nav-utility bg-primary">
            @include('site.components.topbar')
        </div>
        <div class="pt120 pb80 pt-xs-40 pb-xs-16">
            <div class="container">
                @if($settings?->logo_dark)
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <a href="{{ route('site.index') }}">
                            <img alt="Logo" class="logo" src="{{ asset('uploads/' . $settings?->logo_dark) }}" />
                        </a>
                    </div>
                </div>
                @endif
                <!--end of row-->
            </div>
            <!--end of container-->
        </div>
        <div class="module widget-handle mobile-toggle text-center visible-xs">
            <i class="ti-menu"></i>
        </div>
        <div class="nav-bar text-center">
            <div class="container">
                <div class="row">
                    <div class="col-sm-12 text-center">
                        <ul class="menu inline-block mb-xs-16">
                            <li class="top-link hidden-xs">
                                <a href="#top" class="inner-link">
                                    <i class="ti-arrow-up"></i> Top</a>
                            </li>
                            <li>
                                <a href="{{ Request::is('/') ? '#about' : url('/#about') }}"
                                    class="inner-link">Hakkımızda</a>
                                    
                            </li>
                            <li>
                                <a href="{{ route('lead.menu') }}" class="inner-link">Menü</a>
                            </li>
                            <li>
                                <a href="{{ Request::is('/') ? '#special' : url('/#special') }}"
                                    class="inner-link">Spesiyal</a>
                            </li>
                            @if(\App\Models\Gallery::count() > 0)
                            <li>
                                <a href="{{ Request::is('/') ? '#gallery' : url('/#gallery') }}"
                                    class="inner-link">Galeri</a>
                            </li>
                            @endif
                            <li>
                                <a href="{{ Request::is('/') ? '#contact' : url('/#contact') }}" class="inner-link">Bize
                                    Ulaşın</a>
                            </li>
                            
                            <li>
                                <a href="{{ route('site.blog.index') }}">Blog</a>
                            </li>

                            
                        </ul>
                    </div>
                </div>
                <!--end of row-->
            </div>
            <!--end of container-->
        </div>
    </nav>
</div>