<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CarController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Alur mengikuti User Flow di makalah:
| Start -> Buat Akun / Sign In -> Menu Utama (Home, Explore, Saved, Chat,
| Profile, Notification) -> Merek -> Mobil -> Telpon / Chat / Booking ->
| Rental Now / Batalkan -> Kembali Menu / Download.
|
*/

Route::get('/', [AuthController::class, 'start'])->name('start');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/demo-login/{role}', [AuthController::class, 'demoLogin'])->whereIn('role', ['user', 'admin'])->name('demo.login');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('google.redirect');
    Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/home', [HomeController::class, 'home'])->name('home');
    Route::get('/explore', [HomeController::class, 'explore'])->name('explore');
    Route::put('/location', [HomeController::class, 'updateLocation'])->name('location.update');

    Route::get('/cars/{car}', [CarController::class, 'show'])->name('cars.show');
    Route::get('/cars/{car}/call', [CarController::class, 'call'])->name('cars.call');

    Route::get('/saved', [FavoriteController::class, 'index'])->name('saved');
    Route::post('/saved/{car}', [FavoriteController::class, 'toggle'])->name('saved.toggle');

    Route::get('/cars/{car}/booking', [BookingController::class, 'create'])->name('bookings.create');
    Route::get('/promo/check', [BookingController::class, 'checkPromo'])->name('promo.check');
    Route::post('/cars/{car}/booking', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::get('/bookings/{booking}/download', [BookingController::class, 'download'])->name('bookings.download');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/review', [BookingController::class, 'review'])->name('bookings.review');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::get('/chat/messages', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chat', [ChatController::class, 'store'])->name('chat.store');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');
    // Katalog
    Route::resource('cars', Admin\CarController::class)->except('show');
    Route::resource('brands', Admin\BrandController::class)->except('show');
    Route::resource('categories', Admin\CategoryController::class)->except('show');

    // Penjualan
    Route::get('/bookings', [Admin\BookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{booking}', [Admin\BookingController::class, 'show'])->name('bookings.show');
    Route::put('/bookings/{booking}', [Admin\BookingController::class, 'update'])->name('bookings.update');
    Route::resource('promos', Admin\PromoController::class)->except('show');
    Route::resource('payment-methods', Admin\PaymentMethodController::class)->except('show');
    Route::resource('locations', Admin\LocationController::class)->except('show');

    // Pengguna
    Route::resource('users', Admin\UserController::class)->except('show');
    Route::get('/reviews', [Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{booking}', [Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::get('/announcements/create', [Admin\AnnouncementController::class, 'create'])->name('announcements.create');
    Route::post('/announcements', [Admin\AnnouncementController::class, 'store'])->name('announcements.store');

    // Toko
    Route::get('/settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');

    Route::get('/chats', [Admin\ChatController::class, 'index'])->name('chats.index');
    Route::get('/chats/{user}', [Admin\ChatController::class, 'show'])->name('chats.show');
    Route::get('/chats/{user}/messages', [Admin\ChatController::class, 'messages'])->name('chats.messages');
    Route::post('/chats/{user}', [Admin\ChatController::class, 'store'])->name('chats.store');
});
