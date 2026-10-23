<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Brand;
use App\Models\Car;
use App\Models\PaymentMethod;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Mengekspor tampilan website menjadi HTML statis (folder docs/) untuk GitHub Pages.
 * Halaman dirender langsung dari aplikasi Laravel sehingga tampilannya identik;
 * interaksi yang butuh server disimulasikan oleh resources/demo/demo.js.
 */
class ExportStaticDemo extends Command
{
    protected $signature = 'demo:export {--path=docs : Folder tujuan}';

    protected $description = 'Ekspor demo statis Velocity RentCar untuk GitHub Pages';

    /** Host palsu untuk merender halaman; semua URL-nya lalu diubah menjadi relatif. */
    private const HOST = 'http://demo.local';

    private const REPO_URL = 'https://github.com/baricomp4ny/velocity-rentcar';

    /** @var array<string, string> path aplikasi => nama file statis */
    private array $map = [];

    public function handle(HttpKernel $kernel): int
    {
        $out = base_path($this->option('path'));
        $user = User::where('email', config('services.demo_login.accounts.user'))->first();
        $admin = User::where('email', config('services.demo_login.accounts.admin'))->first();

        if (! $user || ! $admin || ! is_file(public_path('build/manifest.json'))) {
            $this->error('Jalankan dulu: php artisan migrate:fresh --seed && npm run build');

            return self::FAILURE;
        }

        // Pesanan contoh agar Info Bayar, Riwayat, dan dashboard admin terisi. Dihapus setelah ekspor.
        $booking = $this->createSampleBooking($user);

        try {
            $pages = $this->pages($user, $admin, $booking);

            foreach ($pages as [, $path, $file]) {
                $this->map[$path] = $file;
            }

            File::deleteDirectory($out);
            File::ensureDirectoryExists($out);

            foreach ($pages as [$actor, $path, $file]) {
                $content = $this->render($kernel, $actor, $path);
                $isHtml = str_ends_with($file, '.html');
                File::put("{$out}/{$file}", $isHtml ? $this->finalize($content, $booking) : $content);
                $this->line("  <info>✓</info> {$file}");
            }
        } finally {
            $booking->delete();
        }

        foreach (['build', 'images'] as $dir) {
            File::copyDirectory(public_path($dir), "{$out}/{$dir}");
        }
        File::copy(public_path('favicon.svg'), "{$out}/favicon.svg");
        File::copy(resource_path('demo/demo.js'), "{$out}/demo.js");
        File::put("{$out}/.nojekyll", '');

        $this->newLine();
        $this->info(count($pages).' halaman diekspor ke '.$this->option('path').'/');

        return self::SUCCESS;
    }

    /**
     * Daftar halaman: [pengguna yang login (null = tamu), path aplikasi, nama file].
     */
    private function pages(User $user, User $admin, Booking $booking): array
    {
        $pages = [
            [null, '/', 'index.html'],
            [null, '/login', 'login.html'],
            [null, '/register', 'register.html'],
            [$user, '/home', 'home.html'],
            [$user, '/explore', 'explore.html'],
            [$user, '/saved', 'saved.html'],
            [$user, '/bookings', 'riwayat.html'],
            [$user, "/bookings/{$booking->code}", 'info-bayar.html'],
            [$user, "/bookings/{$booking->code}/download", 'bukti-pembayaran.pdf'],
            [$user, '/notifications', 'notifikasi.html'],
            [$user, '/profile', 'profil.html'],
            [$user, '/profile/edit', 'edit-profil.html'],
            [$user, '/chat', 'chat.html'],
        ];

        foreach ($user->bookings()->whereKeyNot($booking->id)->get() as $past) {
            $pages[] = [$user, "/bookings/{$past->code}", 'pesanan-'.strtolower($past->code).'.html'];
        }

        foreach (Brand::orderBy('sort_order')->get() as $brand) {
            $pages[] = [$user, "/explore?brand={$brand->id}", 'explore-'.Str::slug($brand->name).'.html'];
        }

        foreach (Car::all() as $car) {
            $slug = Str::slug($car->name);
            $pages[] = [$user, "/cars/{$car->id}", "mobil-{$slug}.html"];
            $pages[] = [$user, "/cars/{$car->id}/booking", "booking-{$slug}.html"];
            $pages[] = [$user, "/cars/{$car->id}/call", "telpon-{$slug}.html"];
        }

        $featured = Car::find(setting('featured_car_id')) ?? Car::first();

        return [
            ...$pages,
            [$admin, '/admin', 'admin.html'],
            [$admin, '/admin/bookings', 'admin-pesanan.html'],
            [$admin, "/admin/bookings/{$booking->code}", 'admin-pesanan-detail.html'],
            [$admin, '/admin/chats', 'admin-chat.html'],
            [$admin, "/admin/chats/{$user->id}", 'admin-chat-pelanggan.html'],
            [$admin, '/admin/cars', 'admin-mobil.html'],
            [$admin, '/admin/cars/create', 'admin-mobil-tambah.html'],
            [$admin, "/admin/cars/{$featured->id}/edit", 'admin-mobil-edit.html'],
            [$admin, '/admin/brands', 'admin-merek.html'],
            [$admin, '/admin/categories', 'admin-kategori.html'],
            [$admin, '/admin/promos', 'admin-promo.html'],
            [$admin, '/admin/payment-methods', 'admin-pembayaran.html'],
            [$admin, '/admin/locations', 'admin-lokasi.html'],
            [$admin, '/admin/users', 'admin-pengguna.html'],
            [$admin, '/admin/reviews', 'admin-ulasan.html'],
            [$admin, '/admin/announcements/create', 'admin-pengumuman.html'],
            [$admin, '/admin/settings', 'admin-pengaturan.html'],
        ];
    }

    private function render(HttpKernel $kernel, ?User $actor, string $path): string
    {
        Auth::forgetGuards();
        if ($actor) {
            Auth::guard('web')->setUser($actor);
        }

        $request = Request::create(self::HOST.$path);
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException("{$path} menghasilkan HTTP {$response->getStatusCode()}");
        }

        return $response instanceof \Symfony\Component\HttpFoundation\BinaryFileResponse
            ? File::get($response->getFile()->getPathname())
            : $response->getContent();
    }

    /**
     * Ubah URL menjadi relatif dan pasang skrip demo.
     */
    private function finalize(string $html, Booking $booking): string
    {
        // href/src menjadi file statis; halaman yang tidak diekspor ditandai #demo-off.
        $html = preg_replace_callback(
            '/\b(href|src)="'.preg_quote(self::HOST, '/').'([^"]*)"/',
            fn ($m) => $m[1].'="'.e($this->target(html_entity_decode($m[2]))).'"',
            $html
        );

        // Form tetap menyimpan path aslinya agar demo.js tahu aksi apa yang disimulasikan.
        $html = preg_replace_callback(
            '/\baction="'.preg_quote(self::HOST, '/').'([^"]*)"/',
            fn ($m) => 'action="#" data-demo-action="'.e(parse_url(html_entity_decode($m[1]) ?: '/', PHP_URL_PATH) ?: '/').'"',
            $html
        );

        $config = [
            'repo' => self::REPO_URL,
            'pages' => $this->map,
            'sampleCode' => $booking->code,
            'cars' => Car::with('brand')->get()->mapWithKeys(fn (Car $car) => [$car->id => $car->modelName()]),
            'promos' => Promo::where('is_active', true)->get()->map(fn (Promo $p) => [
                'code' => $p->code, 'percent' => $p->discount_percent, 'max' => $p->max_discount, 'summary' => $p->summary(),
            ]),
        ];

        $inject = '<script>window.VELOCITY_DEMO = '.json_encode($config, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG).';</script>'
            ."\n".'<script type="module" src="demo.js"></script>'."\n";

        return str_replace('</body>', $inject.'</body>', $html);
    }

    /**
     * Path aplikasi => file statis relatif.
     */
    private function target(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH) ?: '/';
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        if (preg_match('#^/(build|images|storage)/|^/favicon\.svg$#', $path)) {
            return ltrim($path, '/');
        }

        // Explore per merek, dengan pencarian/filter lain tetap di query (diolah demo.js).
        if ($path === '/explore' && isset($query['brand'])) {
            $file = $this->map["/explore?brand={$query['brand']}"] ?? 'explore.html';
            unset($query['brand']);

            return $file.($query ? '?'.http_build_query($query) : '');
        }

        if (! isset($this->map[$path])) {
            return '#demo-off';
        }

        // Pencarian di Home tetap membawa query-nya; halaman lain cukup file-nya.
        return $this->map[$path].(in_array($path, ['/home', '/explore'], true) && $query ? '?'.http_build_query($query) : '');
    }

    private function createSampleBooking(User $user): Booking
    {
        $car = Car::find(setting('featured_car_id')) ?? Car::first();
        $pickup = today()->addWeek();

        return $user->bookings()->create([
            'car_id' => $car->id,
            'pickup_location_id' => $user->location_id,
            'pickup_date' => $pickup,
            'return_date' => $pickup->copy()->addDays(2),
            'price_per_day' => $car->price_per_day,
            'payment_method_id' => PaymentMethod::orderBy('sort_order')->value('id'),
            'status' => 'paid',
        ]);
    }
}
