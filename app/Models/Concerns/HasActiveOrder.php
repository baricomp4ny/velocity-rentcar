<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Data master yang bisa diaktifkan/nonaktifkan dan diurutkan admin
 * (kolom is_active dan sort_order).
 */
trait HasActiveOrder
{
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function initializeHasActiveOrder(): void
    {
        $this->mergeCasts(['is_active' => 'boolean']);
    }
}
