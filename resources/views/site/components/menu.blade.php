@php
    // Kategorileri yayında olan ürünleriyle birlikte çekiyoruz
    $menuCategories = \App\Models\MenuCategory::with(['menuItems' => function($query) {
        $query->where('is_published', 1)->orderBy('order', 'asc');
    }])->where('is_published', 1)->orderBy('order', 'asc')->get();
@endphp

@if($menuCategories->count() > 0)
<style>
    /* Orijinal CSS Stilleriniz */
    @media (max-width: 768px) {
        .tabbed-content .tabs { display: flex !important; flex-wrap: nowrap !important; overflow-x: auto !important; -webkit-overflow-scrolling: touch; align-items: center !important; justify-content: flex-start !important; padding-bottom: 15px !important; scrollbar-width: none; }
        .tabbed-content .tabs::-webkit-scrollbar { display: none; }
        .tabbed-content .tabs li { flex: 0 0 auto !important; margin-bottom: 0 !important; float: none !important; display: inline-block !important; }
        .tabbed-content .tabs li .tab-title { padding: 10px 15px !important; white-space: nowrap !important; }
    }
    .custom-modal { display: none; position: fixed; z-index: 99999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(209, 0, 70, 0.4); backdrop-filter: blur(8px); }
    .modal-content { margin: auto; display: block; width: auto; max-width: 90%; max-height: 75vh; border-radius: 12px; position: relative; top: 50%; transform: translateY(-50%); animation: zoomIn 0.3s ease; }
    @keyframes zoomIn { from {transform: translateY(-50%) scale(0.8);} to {transform: translateY(-50%) scale(1);} }
    .close-modal { position: absolute; top: 20px; right: 30px; color: #fff; font-size: 50px; cursor: pointer; }
    #caption { text-align: center; color: #fff; padding: 10px; position: absolute; bottom: 5%; width: 100%; font-weight: bold; font-size: 20px; }
    .menu-item { cursor: pointer; transition: 0.2s; }
    .menu-item:hover { opacity: 0.8; }
</style>

<a id="menu"></a>
<section class="pb0">
    <div class="container">
        <div class="row mb64">
            <div class="col-sm-12 text-center">
                <div class="ribbon"><h6 class="uppercase mb0">Menü</h6></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-sm-12">
                <div class="tabbed-content text-tabs">
                    <ul class="tabs mb64 mb-xs-24">
                        @foreach($menuCategories as $index => $category)
                            <li class="{{ $index == 0 ? 'active' : '' }}">
                                <div class="tab-title"><span>{{ $category->title }}</span></div>
                                <div class="tab-content">
                                    @php
                                        // Ürünleri iki sütuna dengeli dağıtmak için ikiye bölüyoruz
                                        $items = $category->menuItems;
                                        $half = ceil($items->count() / 2);
                                        $chunks = $items->chunk($half > 0 ? $half : 1);
                                    @endphp

                                    @forelse($chunks as $chunk)
                                        <div class="col-sm-6 p0">
                                            @foreach($chunk as $item)
                                                <div class="mb40 mb-xs-24 {{ $item->image ? 'menu-item' : '' }}" 
                                                     @if($item->image) data-img="{{ asset('uploads/' . $item->image) }}" @endif>
                                                    <span class="bold block">{{ $item->title }}</span>
                                                    @if($item->desc)
                                                        <p class="mb0">{!! strip_tags($item->desc) !!}</p>
                                                    @endif
                                                    @if($item->price)
                                                        <span class="block">{{ number_format($item->price, 0, ',', '.') }}₺</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @empty
                                        <div class="col-sm-12 text-center">
                                            <p>Bu kategoride ürün bulunamadı.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="imageModal" class="custom-modal">
    <span class="close-modal">&times;</span>
    <img class="modal-content" id="imgFull">
    <div id="caption"></div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("imgFull");
    const captionText = document.getElementById("caption");

    document.addEventListener('click', function(e) {
        const item = e.target.closest('.menu-item');
        if (item) {
            const imgSrc = item.getAttribute('data-img');
            const titleElement = item.querySelector('.bold');
            if(imgSrc && titleElement) {
                modal.style.display = "block";
                modalImg.src = imgSrc;
                captionText.innerText = titleElement.innerText;
            }
        }
    });

    document.querySelector(".close-modal").onclick = () => modal.style.display = "none";
    modal.onclick = (e) => { if(e.target !== modalImg) modal.style.display = "none"; };
});
</script>
@endif