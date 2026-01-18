<?php



namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\InstagramPost;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {
        // Sadece onaylanmış olanları, en yeni en üstte olacak şekilde getir
        $instagramPosts = InstagramPost::where('is_published', 1)
            ->orderBy('posted_at', 'desc')
            ->take(6) // Sayfa yapısına göre sayıyı değiştirebilirsin
            ->get();

        return view('site.index', compact('instagramPosts'));
    }
}