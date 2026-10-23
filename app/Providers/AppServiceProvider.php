<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Location;
use App\Models\Message;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Jumlah pelanggan yang chat-nya belum dibalas (badge menu admin & dashboard).
        View::composer(['layouts.admin', 'admin.dashboard'], function ($view) {
            $view->with('unreadChats', Message::unreadFrom('user')->distinct()->count('user_id'));
        });

        // Header pengguna: titik merah notifikasi, pilihan lokasi, dan filter kategori.
        View::composer('components.app-header', function ($view) {
            $view->with([
                'hasUnreadNotifications' => auth()->user()->userNotifications()->whereNull('read_at')->exists(),
                'locations' => Location::active()->ordered()->get(),
                'categories' => Category::active()->ordered()->get(),
            ]);
        });
    }
}
