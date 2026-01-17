<?php

namespace App\Http\Controllers\Site\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;;
use App\Models\BlogCategory;

class IndexController extends Controller
{
    public function index()
    {

    $latestBlogs = Blog::with('category')
            ->where('is_published', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(2);
            

        $blogCategories = BlogCategory::where('is_published', 1)->get();
        return view('site.blog.index', compact(
            'latestBlogs',
            'blogCategories'
        ));
    }
}
