<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\About;
use App\Models\Banner;
use App\Models\Gallery;
use App\Models\Special;
use App\Models\Video;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\InstagramPost;

class IndexController extends Controller
{
    public function index()
    {
        // Singleton Modeller (Tek kayıt)
        $about = About::where('is_published', 1)->first();
        $banner = Banner::where('is_published', 1)->first();

        // Orderable (Sıralı) Modeller
        $galleries = Gallery::where('is_published', 1)->orderBy('order', 'asc')->get();
        $specials = Special::where('is_published', 1)->orderBy('order', 'asc')->get();
        $videos = Video::where('is_published', 1)->orderBy('order', 'asc')->get();

        // Blog Verileri (İlişkisiyle birlikte)
        $latestBlogs = Blog::with('category')
            ->where('is_published', 1)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $blogCategories = BlogCategory::where('is_published', 1)->get();

        // Instagram Postları
        $instagramPosts = InstagramPost::where('is_published', 1)
            ->orderBy('posted_at', 'desc')
            ->take(12)
            ->get();

        return view('site.index', compact(
            'about',
            'banner',
            'galleries',
            'specials',
            'videos',
            'latestBlogs',
            'blogCategories',
            'instagramPosts'
        ));
    }
}