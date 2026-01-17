<?php

namespace App\Http\Controllers\Site\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function kvkk()
    {
        return view('site.permission.kvkk');
    }
    
    public function acikriza()
    {
        return view('site.permission.acikriza');
    }
}
