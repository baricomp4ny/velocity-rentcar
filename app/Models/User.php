<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable;

    public const ROLES = [
        'user' => 'Pelanggan',
        'admin' => 'Admin',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'location_id',
        'role',
        'google_id',
        'avatar',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function initials(): string
    {
        return collect(explode(' ', $this->name))
            ->filter()
            ->take(2)
            ->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))
            ->implode('');
    }

    public function homeUrl(): string
    {
        return $this->isAdmin() ? route('admin.dashboard') : route('home');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Car::class, 'favorites')->withPivot('created_at');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function userNotifications()
    {
        return $this->hasMany(UserNotification::class);
    }

    public function sendNotification(string $title, string $body, ?string $url = null): UserNotification
    {
        return $this->userNotifications()->create(compact('title', 'body', 'url'));
    }
}
