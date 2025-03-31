<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'client_id',
        'user_id',
        'service_date',
        'status',
        'description',
    ];

    protected $casts = [
        'service_date' => 'datetime',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function calendars()
    {
        return $this->hasMany(Calendar::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
