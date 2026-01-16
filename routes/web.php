<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Site\IndexController;
use App\Http\Controllers\Site\Blog\IndexController as BlogIndexController;
use App\Http\Controllers\Site\Menu\IndexController as MenuIndexController;
use Illuminate\Http\Request;


Route::get('/', [IndexController::class, 'index'])->name('site.index');
Route::get('/blog', [BlogIndexController::class, 'index'])->name('site.blog.index');
Route::get('/menu', [MenuIndexController::class, 'index'])->name('site.menu.index');




Route::get('/instagram/callback', function (Request $request) {
    // Eğer Meta bir challenge kodu gönderdiyse, onu direkt ekrana yaz
    if ($request->has('hub_challenge')) {
        return response($request->input('hub_challenge'), 200)
            ->header('Content-Type', 'text/plain');
    }

    
    return "Meta'dan istek bekleniyor...";
});