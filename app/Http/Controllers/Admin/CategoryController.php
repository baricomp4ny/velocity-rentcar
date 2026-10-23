<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;

class CategoryController extends MasterDataController
{
    protected function model(): string
    {
        return Category::class;
    }

    protected function meta(): array
    {
        return [
            'title' => 'Kategori Mobil',
            'subtitle' => 'Jenis mobil yang tampil di kartu dan filter (MPV, SUV, ...)',
            'label' => 'Kategori',
            'route' => 'categories',
            'usage' => ['cars', 'mobil'],
        ];
    }
}
