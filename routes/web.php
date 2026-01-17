<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Site\IndexController;
use App\Http\Controllers\Site\Blog\IndexController as BlogIndexController;
use App\Http\Controllers\Site\Menu\IndexController as MenuIndexController;
use App\Http\Controllers\Site\Permission\IndexController as PermissionIndexController;

use Illuminate\Http\Request;


Route::get('/', [IndexController::class, 'index'])->name('site.index');
Route::get('/blog', [BlogIndexController::class, 'index'])->name('site.blog.index');
Route::get('/menu', [MenuIndexController::class, 'index'])->name('site.menu.index');

Route::get('/kisisel-verilerin-korunmasi-kanunu', [PermissionIndexController::class, 'kvkk'])->name('site.permission.kvkk');
Route::get('/acik-riza-metni', [PermissionIndexController::class, 'acikriza'])->name('site.permission.acikriza');




Route::get('/instagram/callback', function (Request $request) {
    // Eğer Meta bir doğrulama kodu gönderdiyse direkt döndür
    if ($request->has('hub_challenge')) {
        return response($request->input('hub_challenge'), 200)
                 ->header('Content-Type', 'text/plain');
    }

    return "Sistem Hazır. Meta'dan istek bekleniyor...";
});