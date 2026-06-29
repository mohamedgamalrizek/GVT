<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\PublicSiteController;
use Illuminate\Support\Facades\Route;

Route::controller(PublicSiteController::class)->group(function (): void {
    Route::get('/', 'home')->name('home');
    Route::get('/about', 'about')->name('about');
    Route::get('/methodology', 'methodology')->name('methodology');
    Route::get('/theses', 'theses')->name('theses.index');
    Route::get('/theses/{thesis:slug}', 'thesis')->name('theses.show');
    Route::get('/positions', 'positions')->name('positions.index');
    Route::get('/positions/{position:slug}', 'position')->name('positions.show');
    Route::get('/developers', 'developers')->name('developers.index');
    Route::get('/developers/{developer:slug}', 'developer')->name('developers.show');
    Route::get('/certification', 'certification')->name('certification.index');
    Route::get('/certification/developers', 'developers')->name('certification.developers.index');
    Route::get('/certification/developers/{developer:slug}', 'developer')->name('certification.developers.show');
    Route::get('/insights', 'insights')->name('insights.index');
    Route::get('/insights/{post:slug}', 'insight')->name('insights.show');
    Route::get('/newsletter', 'newsletter')->name('newsletter.index');
    Route::post('/newsletter', 'subscribe')->name('newsletter.store');
    Route::get('/contact', 'contact')->name('contact.index');
    Route::post('/contact', 'storeContact')->name('contact.store');
});

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::redirect('dashboard', '/admin')->name('dashboard');

    Route::get('/admin', DashboardController::class)->name('admin.dashboard');
    Route::post('/admin/theses/{thesis}/featured-image', [ResourceController::class, 'uploadThesisImage'])->name('admin.theses.featured-image');
    Route::get('/admin/{resource}', [ResourceController::class, 'index'])->name('admin.resources.index');
    Route::post('/admin/{resource}', [ResourceController::class, 'store'])->name('admin.resources.store');
    Route::put('/admin/{resource}/{id}', [ResourceController::class, 'update'])->name('admin.resources.update');
    Route::delete('/admin/{resource}/{id}', [ResourceController::class, 'destroy'])->name('admin.resources.destroy');
});

require __DIR__.'/settings.php';
