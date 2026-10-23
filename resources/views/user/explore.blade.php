@extends('layouts.app')

@section('title', 'Explore')
@section('bottom-nav', 'explore')

@section('content')
    <x-app-header :filters="$filters" :action="route('explore')"/>

    <div x-data="brandPicker(@js(['selected' => $selected, 'names' => $brands->pluck('name', 'id')]))"
         class="container-page space-y-6 px-5 pt-2 lg:space-y-10 lg:px-8 lg:pt-8">

        {{-- Pilih merek: ketuk beberapa logo untuk menggabungkan hasilnya --}}
        <section>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-base font-semibold lg:text-xl">Brands</h2>
                <p class="text-[11px] text-grey lg:text-xs">Pilih satu atau beberapa merek</p>
            </div>
            <div class="-mx-5 flex gap-3 overflow-x-auto px-5 pb-1 lg:mx-0 lg:flex-wrap lg:gap-5 lg:px-0">
                <button type="button" @click="selectAll()" class="flex w-14 flex-none flex-col items-center gap-1.5 lg:w-20">
                    <span class="flex h-14 w-14 items-center justify-center rounded-full text-xs font-semibold transition lg:h-16 lg:w-16"
                          :class="selected.length ? 'bg-neutral text-ink lg:bg-white lg:shadow-sm' : 'bg-primary text-white shadow-lg shadow-primary/30'">Semua</span>
                    <span class="text-[11px] font-medium lg:text-xs" :class="selected.length ? 'text-ink/80' : 'text-primary'">Semua</span>
                </button>
                @foreach ($brands as $brand)
                    <button type="button" @click="toggle(@js($brand->id))" aria-label="{{ $brand->name }}"
                            class="flex w-14 flex-none flex-col items-center gap-1.5 lg:w-20">
                        <span class="flex h-14 w-14 items-center justify-center rounded-full transition lg:h-16 lg:w-16"
                              :class="isActive(@js($brand->id)) ? 'bg-primary shadow-lg shadow-primary/30' : 'bg-neutral hover:bg-primary-50 lg:bg-white lg:shadow-sm'">
                            <img src="{{ $brand->logoUrl() }}" alt="" class="h-7 w-7 object-contain lg:h-8 lg:w-8"
                                 :class="isActive(@js($brand->id)) && 'brightness-0 invert'">
                        </span>
                        <span class="whitespace-nowrap text-[11px] font-medium lg:text-xs" :class="isActive(@js($brand->id)) ? 'text-primary' : 'text-ink/80'">{{ $brand->name }}</span>
                    </button>
                @endforeach
            </div>
        </section>

        <section>
            <div class="mb-3 flex items-center justify-between gap-3 lg:mb-5">
                <h2 class="truncate text-base font-semibold lg:text-xl">
                    <span x-text="title">Semua Mobil</span>
                    <span class="text-sm font-normal text-grey" x-text="`(${visible})`"></span>
                </h2>
                <button type="button" x-show="selected.length" x-cloak @click="selectAll()" class="flex-none text-xs font-medium text-primary lg:text-sm">Reset</button>
            </div>

            <div class="grid gap-4 lg:grid-cols-3 lg:gap-6">
                @foreach ($cars as $car)
                    <x-car-card :car="$car" :saved="in_array($car->id, $favoriteIds)"/>
                @endforeach
            </div>

            <div x-show="!visible" x-cloak class="rounded-3xl bg-neutral px-6 py-12 text-center">
                <x-icon name="car" class="mx-auto h-10 w-10 text-grey"/>
                <p class="mt-3 font-medium">Belum ada mobil</p>
                <p class="mt-1 text-sm text-grey">Coba pilih merek lain atau ubah pencarian.</p>
            </div>
        </section>
    </div>
@endsection
