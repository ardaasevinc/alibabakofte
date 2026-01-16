
@if($setting?->phone)
    <style>
        /* WhatsApp Hareketli Gradient Konteynırı */
        .wp-float-btn {
            position: fixed;
            bottom: 30px;
            /* Instagram butonunun üstünde durması için (ayarlanabilir) */
            right: 30px;
            z-index: 9999;
            padding: 2px;
            border-radius: 50px;
            overflow: hidden;
            display: flex;
            align-items: center;
            text-decoration: none !important;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .wp-float-btn:hover {
            transform: scale(1.08);
            box-shadow: 0 15px 35px rgba(37, 211, 102, 0.4);
        }

        /* WhatsApp Yeşil Tonlarında Dönen Gradient */
        .wp-bg-rotate {
            position: absolute;
            width: 300%;
            height: 300%;
            background: conic-gradient(from 0deg at 50% 50%, #25D366, #128C7E, #075E54, #128C7E, #25D366);
            animation: wp-rotate 4s linear infinite;
            z-index: -1;
            left: -100%;
            top: -100%;
        }

        @keyframes wp-rotate {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* İçerik Alanı */
        .wp-inner-content {
            background: #0b141a;
            /* WhatsApp Web Karanlık Mod rengi */
            padding: 12px 24px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
        }

        .wp-text {
            font-weight: 600;
            font-size: 16px;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        /* Profil Resimleri Grubu */
        .wp-avatars {
            display: flex;
            align-items: center;
            margin-left: 5px;
        }

        .wp-avatars img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid #0b141a;
            margin-left: -12px;
            object-fit: cover;
        }

        /* Mobil Uyumluluk */
        @media (max-width: 768px) {
            .wp-float-btn {
                bottom: 30px;
                /* Instagram butonunun üzerinde */
                right: 50%;
                transform: translateX(50%);
                width: max-content;
            }

            .wp-float-btn:hover {
                transform: translateX(50%) scale(1.05);
            }

            .wp-inner-content {
                padding: 10px 20px;
            }

            .wp-text {
                font-size: 15px;
            }

            .wp-avatars img {
                width: 24px;
                height: 24px;
            }
        }
    </style>

    <a href="https://wa.me/{{ preg_replace('/\D/', '', $setting->phone) }}" target="_blank" class="wp-float-btn">





        <div class="wp-bg-rotate"></div>

        <div class="wp-inner-content">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path
                    d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.94 3.659 1.437 5.634 1.437h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z" />
            </svg>

            <span class="wp-text">WhatsApp</span>

            <div class="wp-avatars">
                <img src="https://i.pravatar.cc/150?u=4" alt="Destek 1">
                <img src="https://i.pravatar.cc/150?u=5" alt="Destek 2">
            </div>
        </div>
    </a>

@endif