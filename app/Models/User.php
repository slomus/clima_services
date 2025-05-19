<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

       /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'hash',
        'address_city_id',
        'address_street',
        'address_home_number',
        'address_apartment_number',
        'address_post_code',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'hash',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }


    public function citys()
    {
        return $this->belongsTo(City::class, 'address_city_id');
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'user_id');
    }

    public function servicesAsClient()
    {
        return $this->hasMany(Service::class, 'client_id');
    }

    public function messagesSent()
    {
        return $this->hasMany(Message::class, 'from');
    }

    public function messagesReceived()
    {
        return $this->hasMany(Message::class, 'to');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function calendars()
    {
        return $this->hasMany(Calendar::class);
    }
}
