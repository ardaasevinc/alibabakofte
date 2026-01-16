<style>
    /* Mobil Kategoriler İçin Yatay Scroll */
    @media (max-width: 768px) {
        .tabbed-content .tabs {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch;
            align-items: center !important;
            justify-content: flex-start !important;
            padding-bottom: 15px !important;
            scrollbar-width: none;
        }

        .tabbed-content .tabs::-webkit-scrollbar { display: none; }

        .tabbed-content .tabs li {
            flex: 0 0 auto !important;
            margin-bottom: 0 !important;
            float: none !important;
            display: inline-block !important;
        }

        .tabbed-content .tabs li .tab-title {
            padding: 10px 15px !important;
            white-space: nowrap !important;
        }
    }

    /* Görsel Pop-up (Modal) Stilleri */
    .custom-modal {
        display: none;
        position: fixed;
        z-index: 99999;
        left: 0; top: 0;
        width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.9);
        backdrop-filter: blur(8px);
    }

    .modal-content {
        margin: auto;
        display: block;
        width: auto;
        max-width: 90%;
        max-height: 75vh;
        border-radius: 12px;
        position: relative;
        top: 50%;
        transform: translateY(-50%);
        animation: zoomIn 0.3s ease;
    }

    @keyframes zoomIn { from {transform: translateY(-50%) scale(0.8);} to {transform: translateY(-50%) scale(1);} }

    .close-modal {
        position: absolute;
        top: 20px; right: 30px;
        color: #fff; font-size: 50px; cursor: pointer;
    }

    #caption {
        text-align: center; color: #fff; padding: 10px;
        position: absolute; bottom: 5%; width: 100%; font-weight: bold; font-size: 20px;
    }

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
                        <li class="active">
                            <div class="tab-title"><span>Köftelerimiz</span></div>
                            <div class="tab-content">
                                <div class="col-sm-6 p0">
                                    <div class="mb40 mb-xs-24 menu-item" data-img="{{ asset('site/img/body1.jpg') }}">
                                        <span class="bold block">Ali Baba Köfte</span>
                                        <p class="mb0">Pilav, domates, özel sos, kızarmış biber ve ekmek ile.</p>
                                        <span class="block">450₺</span>
                                    </div>
                                    <div class="mb40 mb-xs-24 menu-item" data-img="{{ asset('site/img/body1.jpg') }}">
                                        <span class="bold block">Cheddarlı Köfte</span>
                                        <p class="mb0">Yoğun istek üzerine hazırlanan özel erimiş cheddarlı.</p>
                                        <span class="block">450₺</span>
                                    </div>
                                    <div class="mb40 mb-xs-24 menu-item" data-img="{{ asset('site/img/body1.jpg') }}">
                                        <span class="bold block">Kaşarlı Köfte</span>
                                        <p class="mb0">Ali Baba köftesi ile kaliteli kaşarın buluşması.</p>
                                        <span class="block">450₺</span>
                                    </div>
                                </div>
                                <div class="col-sm-6 p0">
                                    <div class="mb40 mb-xs-24 menu-item" data-img="{{ asset('site/img/body1.jpg') }}">
                                        <span class="bold block">Acılı Köfte</span>
                                        <p class="mb0">Ali Baba'nın eşsiz baharatlı ve acılı özel tarifi.</p>
                                        <span class="block">450₺</span>
                                    </div>
                                    <div class="mb40 mb-xs-24">
                                        <span class="bold block">Yarım Porsiyon Köfte</span>
                                        <p class="mb0">Daha hafif bir tercih arayanlar için.</p>
                                        <span class="block">230₺</span>
                                    </div>
                                    <div class="mb40 mb-xs-24">
                                        <span class="bold block">Yarım Ekmek Köfte</span>
                                        <p class="mb0">Hızlı ve lezzetli Ali Baba klasiği.</p>
                                        <span class="block">230₺</span>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="tab-title"><span>Sucuk</span></div>
                            <div class="tab-content">
                                <div class="col-sm-6 p0">
                                    <div class="mb40 mb-xs-24 menu-item" data-img="{{ asset('site/img/body1.jpg') }}">
                                        <span class="bold block">Porsiyon Dana Sucuk</span>
                                        <p class="mb0">%100 dana etinden, çiftliğimizden doğal lezzet.</p>
                                        <span class="block">450₺</span>
                                    </div>
                                   
                                </div>
                                
                            </div>
                        </li>
                        <li>
                            <div class="tab-title"><span>Çorbalar</span></div>
                            <div class="tab-content">
                                <div class="col-sm-6 p0">
                                    <div class="mb40 mb-xs-24 menu-item" data-img="{{ asset('site/img/body1.jpg') }}">
                                        <span class="bold block">Beykoz Paça</span>
                                        <p class="mb0">En çok aranan, özel terbiyeli paça çorbamız.</p>
                                        <span class="block">200₺</span>
                                    </div>
                                    <div class="mb40 mb-xs-24 menu-item" data-img="{{ asset('site/img/body1.jpg') }}">
                                        <span class="bold block">Tavuk Suyu</span>
                                        <p class="mb0">Eşsiz tavuk suyu çorbamız.</p>
                                        <span class="block">150₺</span>
                                    </div>
                                </div>
                                <div class="col-sm-6 p0">
                                    <div class="mb40 mb-xs-24 menu-item" data-img="{{ asset('site/img/body1.jpg') }}">
                                        <span class="bold block">Mercimek Çorbası</span>
                                        <p class="mb0">27 senedir değişmeyen mercimek çorbamız.</p>
                                        <span class="block">100₺</span>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="tab-title"><span>Salatalar</span></div>
                            <div class="tab-content">
                                <div class="col-sm-6 p0">
                                    <div class="mb40 mb-xs-24 menu-item" data-img="{{ asset('site/img/body1.jpg') }}">
                                        <span class="bold block">Kayık Piyaz</span>
                                        <p class="mb0">Piyazın en lezzetli hali.</p>
                                        <span class="block">250₺</span>
                                    </div>
                                    <div class="mb40 mb-xs-24 menu-item" data-img="{{ asset('site/img/body1.jpg') }}">
                                        <span class="bold block">Duble Çoban Salata</span>
                                        <p class="mb0">Doğal lezzetler ile hazırlanan taze salata.</p>
                                        <span class="block">220₺</span>
                                    </div>
                                </div>
                               
                            </div>
                        </li>
                        <li>
                            <div class="tab-title"><span>Tatlılar</span></div>
                            <div class="tab-content">
                                <div class="col-sm-6 p0">
                                    <div class="mb40 mb-xs-24 menu-item" data-img="{{ asset('site/img/body1.jpg') }}">
                                        <span class="bold block">Hayrabolu Tatlısı</span>
                                        <p class="mb0">Tahin ve bol ceviz ile servis edilir.</p>
                                        <span class="block">130₺</span>
                                    </div>
                                </div>
                                <div class="col-sm-6 p0">
                                    <div class="mb40 mb-xs-24 menu-item" data-img="{{ asset('site/img/body1.jpg') }}">
                                        <span class="bold block">Fırın Sütlaç</span>
                                        <p class="mb0">Günlük taze sütten hazırlanan hafif lezzet.</p>
                                        <span class="block">130₺</span>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="tab-title"><span>İçecekler</span></div>
                            <div class="tab-content">
                                <div class="col-sm-6 p0">
                                    <div class="mb40 mb-xs-24">
                                        <span class="bold block">Ayran</span>
                                        <span class="block">60₺</span>
                                    </div>
                                </div>
                                <div class="col-sm-6 p0">
                                    <div class="mb40 mb-xs-24">
                                        <span class="bold block">Coca Cola / Meşrubat</span>
                                        <span class="block">70₺</span>
                                    </div>
                                </div>
                            </div>
                        </li>
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
            const title = item.querySelector('.bold').innerText;
            if(imgSrc) {
                modal.style.display = "block";
                modalImg.src = imgSrc;
                captionText.innerText = title;
            }
        }
    });

    document.querySelector(".close-modal").onclick = () => modal.style.display = "none";
    modal.onclick = (e) => { if(e.target !== modalImg) modal.style.display = "none"; };
});
</script>