<?php

namespace App\Http\Controllers\Site\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {
        return view('site.blog.index');
    }
}
