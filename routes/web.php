<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Site\IndexController;
use App\Http\Controllers\Site\Blog\IndexController as BlogIndexController;
use App\Http\Controllers\Site\Menu\IndexController as MenuIndexController;
use Illuminate\Http\Request;


Route::get('/', [IndexController::class, 'index'])->name('site.index');
Route::get('/blog', [BlogIndexController::class, 'index'])->name('site.blog.index');
Route::get('/menu', [MenuIndexController::class, 'index'])->name('site.menu.index');




Route::get('/instagram/callback', function (Illuminate\Http\Request $request) {
    // Meta'nın gönderdiği token ile sizin config'deki token eşleşmeli
    if ($request->input('hub_verify_token') === config('services.instagram.verify_token')) {
        // Hem hub_challenge hem de hub.challenge (hub_challenge olarak gelir) kontrolü
        return response($request->input('hub_challenge'), 200)
                 ->header('Content-Type', 'text/plain');
    }

    return response('Token mismatch veya istek hatalı', 403);
});