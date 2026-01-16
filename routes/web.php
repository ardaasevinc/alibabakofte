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
    // Meta'nın gönderdiği verify_token ile sizin belirlediğiniz eşleşmeli
    if ($request->input('hub_verify_token') === env('INSTAGRAM_VERIFY_TOKEN')) {
        return response($request->input('hub_challenge'));
    }
    return response('Token mismatch', 403);
});