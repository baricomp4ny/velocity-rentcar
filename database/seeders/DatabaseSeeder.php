<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Car;
use App\Models\Category;
use App\Models\Location;
use App\Models\PaymentMethod;
use App\Models\Promo;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Akun demo (password semuanya: "password"):
     * - Admin : admin@velocity.test
     * - User  : brot@velocity.test (persona di makalah), rina@velocity.test, dimas@velocity.test
     */
    public function run(): void
    {
        $locations = collect(['Bogor Barat', 'Bogor Timur', 'Bogor Utara', 'Bogor Selatan', 'Bogor Tengah', 'Tanah Sareal', 'Kabupaten Bogor'])
            ->mapWithKeys(fn ($name, $i) => [$name => Location::create(['name' => $name, 'sort_order' => $i])]);

        $categories = collect(['Hatchback', 'MPV', 'SUV', 'Sedan', 'Crossover', 'Coupe'])
            ->mapWithKeys(fn ($name, $i) => [$name => Category::create(['name' => $name, 'sort_order' => $i])]);

        $payments = collect([
            ['Debit BCA', 'debit'], ['Debit Mandiri', 'debit'], ['Debit BRI', 'debit'], ['Debit BNI', 'debit'],
            ['GoPay', 'ewallet'], ['OVO', 'ewallet'], ['DANA', 'ewallet'],
        ])->mapWithKeys(fn ($p, $i) => [$p[0] => PaymentMethod::create(['name' => $p[0], 'type' => $p[1], 'sort_order' => $i])]);

        User::create([
            'name' => 'Admin Velocity',
            'email' => 'admin@velocity.test',
            'phone' => '081200000001',
            'role' => 'admin',
            'password' => 'password',
        ]);

        $customers = collect([
            ['Brot', 'brot@velocity.test', '081200000002', 'Jl. Pajajaran No. 10, Bogor', 'Bogor Barat'],
            ['Rina Kusuma', 'rina@velocity.test', '081200000003', 'Jl. Suryakencana No. 5, Bogor', 'Bogor Tengah'],
            ['Dimas Pratama', 'dimas@velocity.test', '081200000004', 'Jl. Raya Tajur No. 21, Bogor', 'Bogor Timur'],
        ])->map(fn ($c) => User::create([
            'name' => $c[0], 'email' => $c[1], 'phone' => $c[2], 'address' => $c[3], 'location_id' => $locations[$c[4]]->id, 'password' => 'password',
        ]));

        $brands = collect(['Toyota', 'Honda', 'Hyundai', 'Suzuki', 'BMW'])->mapWithKeys(fn ($name, $i) => [
            $name => Brand::create(['name' => $name, 'logo' => 'images/brands/'.strtolower($name).'.svg', 'sort_order' => $i]),
        ]);

        $cars = collect($this->cars())->mapWithKeys(function (array $data) use ($brands, $categories) {
            [$brand, $category, $photos] = [$data['brand'], $data['category'], $data['photos']];
            unset($data['brand'], $data['category'], $data['photos']);

            $car = $brands[$brand]->cars()->create([...$data, 'category_id' => $categories[$category]->id]);
            foreach ($photos as $order => $path) {
                $car->photos()->create(['path' => "images/cars/{$path}", 'sort_order' => $order]);
            }

            return [$car->name => $car];
        });

        Promo::create(['code' => 'VELOCITY10', 'description' => 'Diskon 10% untuk semua mobil', 'discount_percent' => 10, 'max_discount' => 200000]);
        Promo::create(['code' => 'BOGORHEMAT', 'description' => 'Diskon 15% khusus warga Bogor', 'discount_percent' => 15, 'max_discount' => 300000]);

        // Riwayat sewa yang sudah selesai + ulasan, sumber rating tiap mobil.
        $reviews = [
            ['BMW M4 Competition', [5, 5, 5], 'Tenaganya luar biasa dan kabinnya sporty, cocok untuk acara spesial.'],
            ['Toyota Innova Zenix Q 2.0', [5, 5, 5], 'Mobilnya bersih, irit, dan nyaman untuk keluarga.'],
            ['Toyota Land Cruiser Prado', [5, 5, 4], 'Gagah dan sangat nyaman untuk ke Puncak.'],
            ['Hyundai Palisade Signature', [5, 5, 5], 'Kabin luas dan mewah, pelayanan cepat.'],
            ['Toyota Kijang Innova 2.4Q', [5, 5, 4], 'Mesin diesel bertenaga, cocok untuk perjalanan jauh.'],
            ['Honda HR-V Turbo RS', [5, 4, 5], 'Tarikan mantap dan fitur lengkap.'],
            ['Suzuki Jimny', [5, 5, 4], 'Seru dipakai off-road ringan.'],
            ['Hyundai Ioniq 5', [5, 4, 5], 'Senyap dan baterainya awet.'],
            ['Toyota Rush GR Sport', [5, 4, 5], 'Ground clearance tinggi, aman di jalan berbukit.'],
            ['Suzuki Ertiga Hybrid', [5, 4, 5], 'Irit untuk keliling Bogor.'],
            ['Honda City Sedan', [5, 4, 5], 'Nyaman untuk perjalanan bisnis.'],
            ['Honda Brio RS', [5, 4, 5], 'Lincah dan mudah diparkir.'],
            ['Hyundai Stargazer', [5, 4, 4], 'Kabin lega untuk 7 orang.'],
            ['Suzuki XL7 Alpha', [4, 5, 5], 'Tampilan gagah, nyaman dikendarai.'],
        ];

        foreach ($reviews as $i => [$carName, $ratings, $review]) {
            foreach ($ratings as $j => $rating) {
                $pickup = Carbon::parse('2025-05-01')->addDays($i * 7 + $j * 2);
                $this->completedBooking($customers[$j], $cars[$carName], $payments['Debit BCA'], $pickup, $rating, $j === 0 ? $review : null);
            }
        }

        // Poster Home menampilkan BMW M4 (bisa diganti di Admin > Pengaturan Toko).
        Setting::saveMany(['featured_car_id' => $cars['BMW M4 Competition']->id]);

        $brot = $customers[0];
        $brot->favorites()->attach($cars['Toyota Innova Zenix Q 2.0']->id);
        $brot->sendNotification('Selamat datang di '.setting('store_name'), 'Temukan mobil impianmu dan nikmati layanan rental mobil untuk daerah Bogor dan sekitarnya.', '/home');
        $brot->messages()->create(['sender' => 'admin', 'body' => 'Halo kak, selamat datang di '.setting('store_name').'! Ada yang bisa kami bantu?']);
    }

    private function completedBooking(User $user, Car $car, PaymentMethod $payment, Carbon $pickup, int $rating, ?string $review): void
    {
        $booking = $user->bookings()->make([
            'car_id' => $car->id,
            'pickup_location_id' => $user->location_id,
            'pickup_date' => $pickup,
            'return_date' => $pickup->copy()->addDay(),
            'price_per_day' => $car->price_per_day,
            'payment_method_id' => $payment->id,
            'status' => 'completed',
            'rating' => $rating,
            'review' => $review,
        ]);

        $booking->created_at = $pickup->copy()->subDays(2);
        $booking->save();
    }

    private function cars(): array
    {
        return [
            [
                'brand' => 'BMW', 'name' => 'BMW M4 Competition', 'model_code' => 'G82', 'category' => 'Coupe', 'transmission' => 'Matic',
                'fuel' => 'Bensin', 'fuel_grade' => 'RON 98', 'seats' => 4, 'year' => 2023, 'power_hp' => 503, 'acceleration' => 3.9,
                'price_per_day' => 5000000, 'is_popular' => true,
                'photos' => ['bmw-m4-competition-front.jpg', 'bmw-m4-competition-interior.jpg', 'bmw-m4-competition-rear.jpg'],
                'description' => 'BMW M4 Competition generasi G82 adalah coupe performa tinggi dengan mesin M TwinPower Turbo 6 silinder 3.0 liter bertenaga 503 HP. Desainnya agresif dengan gril ginjal besar, kabin sporty dengan Curved Display dan jok M Carbon, serta akselerasi 0-100 km/jam sekitar 3,9 detik. Pilihan sempurna untuk acara spesial, pemotretan, maupun pengalaman berkendara yang tak terlupakan.',
            ],
            [
                'brand' => 'Toyota', 'name' => 'Toyota Innova Zenix Q 2.0', 'category' => 'MPV', 'transmission' => 'Matic',
                'fuel' => 'Hybrid', 'fuel_grade' => 'RON 92', 'seats' => 7, 'year' => 2023, 'price_per_day' => 1500000, 'is_popular' => true,
                'photos' => ['toyota-innova-zenix.jpg', 'toyota-innova-zenix-showroom.jpg', 'toyota-innova-zenix-front.jpg', 'toyota-innova-zenix-street.jpg'],
                'description' => 'Toyota Kijang Innova Zenix dipastikan memiliki tingkat kenyamanan yang jauh lebih baik dibandingkan dengan generasi sebelumnya. Hal ini tak terlepas dari platform yang diadopsi. Sebelumnya, Kijang Innova selalu menggunakan sasis ladder frame, kini Zenix memakai platform TNGA monokok dengan penggerak roda depan sehingga kabin lebih lega, senyap, dan irit berkat mesin hybrid.',
            ],
            [
                'brand' => 'Toyota', 'name' => 'Toyota Kijang Innova 2.4Q', 'category' => 'MPV', 'transmission' => 'Matic',
                'fuel' => 'Diesel', 'fuel_grade' => 'Dexlite', 'seats' => 7, 'year' => 2021, 'price_per_day' => 750000, 'is_popular' => true,
                'photos' => ['toyota-kijang-innova-24q.jpg'],
                'description' => 'Kijang Innova 2.4Q adalah MPV keluarga yang tangguh dengan mesin diesel bertenaga dan irit. Kabin luas untuk 7 penumpang, cocok untuk perjalanan keluarga maupun dinas ke luar kota.',
            ],
            [
                'brand' => 'Toyota', 'name' => 'Toyota Rush GR Sport', 'category' => 'SUV', 'transmission' => 'Matic',
                'fuel' => 'Bensin', 'fuel_grade' => 'RON 92', 'seats' => 7, 'year' => 2024, 'price_per_day' => 600000, 'is_popular' => false,
                'photos' => ['toyota-rush-gr-sport.jpg'],
                'description' => 'Toyota Rush GR Sport hadir dengan tampilan sporty, ground clearance tinggi, dan kabin tiga baris. Ideal untuk menjelajah Bogor yang berbukit dengan nyaman.',
            ],
            [
                'brand' => 'Toyota', 'name' => 'Toyota Land Cruiser Prado', 'category' => 'SUV', 'transmission' => 'Matic',
                'fuel' => 'Diesel', 'fuel_grade' => 'Dexlite', 'seats' => 7, 'year' => 2022, 'price_per_day' => 2500000, 'is_popular' => true,
                'photos' => ['toyota-land-cruiser-prado.jpg'],
                'description' => 'Land Cruiser Prado adalah SUV premium berpenggerak 4x4 dengan kenyamanan kelas atas. Pilihan tepat untuk acara spesial, tamu VIP, maupun perjalanan off-road.',
            ],
            [
                'brand' => 'Honda', 'name' => 'Honda HR-V Turbo RS', 'category' => 'Crossover', 'transmission' => 'Matic',
                'fuel' => 'Bensin', 'fuel_grade' => 'RON 92', 'seats' => 5, 'year' => 2023, 'price_per_day' => 700000, 'is_popular' => true,
                'photos' => ['honda-hr-v-turbo-rs.jpg'],
                'description' => 'Honda HR-V Turbo RS memadukan desain crossover yang elegan dengan mesin turbo yang responsif. Dilengkapi Honda Sensing untuk keamanan berkendara di dalam kota.',
            ],
            [
                'brand' => 'Honda', 'name' => 'Honda Brio RS', 'category' => 'Hatchback', 'transmission' => 'Manual',
                'fuel' => 'Bensin', 'fuel_grade' => 'RON 90', 'seats' => 5, 'year' => 2023, 'price_per_day' => 500000, 'is_popular' => false,
                'photos' => ['honda-brio-rs.jpg'],
                'description' => 'Honda Brio RS adalah city car lincah dan irit bahan bakar, mudah diparkir dan cocok untuk mobilitas harian di pusat kota Bogor.',
            ],
            [
                'brand' => 'Honda', 'name' => 'Honda City Sedan', 'category' => 'Sedan', 'transmission' => 'Matic',
                'fuel' => 'Bensin', 'fuel_grade' => 'RON 92', 'seats' => 5, 'year' => 2021, 'price_per_day' => 550000, 'is_popular' => false,
                'photos' => ['honda-city-sedan.jpg'],
                'description' => 'Honda City Sedan menawarkan kabin yang senyap dan bagasi lapang. Cocok untuk perjalanan bisnis dengan kesan profesional.',
            ],
            [
                'brand' => 'Hyundai', 'name' => 'Hyundai Palisade Signature', 'category' => 'SUV', 'transmission' => 'Matic',
                'fuel' => 'Diesel', 'fuel_grade' => 'Dexlite', 'seats' => 7, 'year' => 2023, 'price_per_day' => 2000000, 'is_popular' => true,
                'photos' => ['hyundai-palisade-signature.jpg'],
                'description' => 'Hyundai Palisade adalah SUV besar dengan interior mewah, kursi kapten baris kedua, dan fitur keselamatan lengkap untuk kenyamanan seluruh keluarga.',
            ],
            [
                'brand' => 'Hyundai', 'name' => 'Hyundai Ioniq 5', 'category' => 'Crossover', 'transmission' => 'Matic',
                'fuel' => 'Listrik', 'fuel_grade' => 'Baterai 72,6 kWh', 'seats' => 5, 'year' => 2023, 'price_per_day' => 1200000, 'is_popular' => false,
                'photos' => ['hyundai-ioniq-5.jpg'],
                'description' => 'Hyundai Ioniq 5 adalah mobil listrik dengan jarak tempuh jauh dan pengisian cepat. Bebas emisi dan sangat senyap untuk berkendara di dalam kota.',
            ],
            [
                'brand' => 'Hyundai', 'name' => 'Hyundai Stargazer', 'category' => 'MPV', 'transmission' => 'Matic',
                'fuel' => 'Bensin', 'fuel_grade' => 'RON 92', 'seats' => 7, 'year' => 2022, 'price_per_day' => 550000, 'is_popular' => false,
                'photos' => ['hyundai-stargazer.jpg'],
                'description' => 'Hyundai Stargazer adalah MPV modern dengan kabin lega dan fitur lengkap dengan harga sewa yang bersahabat.',
            ],
            [
                'brand' => 'Suzuki', 'name' => 'Suzuki Ertiga Hybrid', 'category' => 'MPV', 'transmission' => 'Matic',
                'fuel' => 'Hybrid', 'fuel_grade' => 'RON 92', 'seats' => 7, 'year' => 2023, 'price_per_day' => 500000, 'is_popular' => false,
                'photos' => ['suzuki-ertiga-hybrid.jpg'],
                'description' => 'Suzuki Ertiga Hybrid adalah MPV keluarga yang irit berkat teknologi Smart Hybrid. Nyaman untuk tujuh penumpang.',
            ],
            [
                'brand' => 'Suzuki', 'name' => 'Suzuki XL7 Alpha', 'category' => 'SUV', 'transmission' => 'Manual',
                'fuel' => 'Hybrid', 'fuel_grade' => 'RON 92', 'seats' => 7, 'year' => 2025, 'price_per_day' => 550000, 'is_popular' => false,
                'photos' => ['suzuki-xl7-alpha.jpg'],
                'description' => 'Suzuki XL7 Alpha adalah SUV tujuh penumpang dengan tampilan gagah dan ground clearance tinggi.',
            ],
            [
                'brand' => 'Suzuki', 'name' => 'Suzuki Jimny', 'category' => 'SUV', 'transmission' => 'Manual',
                'fuel' => 'Bensin', 'fuel_grade' => 'RON 92', 'seats' => 4, 'year' => 2019, 'price_per_day' => 800000, 'is_popular' => false,
                'photos' => ['suzuki-jimny.jpg'],
                'description' => 'Suzuki Jimny adalah SUV kompak 4x4 legendaris yang siap diajak berpetualang ke jalur Puncak dan sekitarnya.',
            ],
        ];
    }
}
