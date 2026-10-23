<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Message extends Model
{
    use HasUuids;

    protected $fillable = ['sender', 'body', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pesan baru sejak waktu tertentu (untuk polling chat).
     */
    public function scopeSince(Builder $query, ?string $since): Builder
    {
        return $query
            ->when($since, fn ($q) => $q->where('created_at', '>=', Carbon::parse($since)))
            ->oldest();
    }

    public function scopeUnreadFrom(Builder $query, string $sender): Builder
    {
        return $query->where('sender', $sender)->whereNull('read_at');
    }

    public function toChatArray(): array
    {
        return [
            'id' => $this->id,
            'sender' => $this->sender,
            'body' => $this->body,
            'time' => $this->created_at->format('H:i'),
            'at' => $this->created_at->toIso8601String(),
        ];
    }
}
