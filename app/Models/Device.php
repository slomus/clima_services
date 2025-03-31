<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'model',
        'producent_number',
        'serial_number',
        'purchase_date',
        'warranty_end_date',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_end_date' => 'date',
    ];

    // Relacja do użytkownika (klienta)
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // Relacja do serwisów
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    // Sprawdzenie czy urządzenie jest na gwarancji
    public function isUnderWarranty()
    {
        if (!$this->warranty_end_date) {
            return false;
        }

        return Carbon::now()->lte($this->warranty_end_date);
    }

    public function needsService()
    {
        $lastService = $this->services()
            ->where('status', 'completed')
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastService) {
            if ($this->purchase_date) {
                return $this->purchase_date->diffInMonths(Carbon::now()) >= 12;
            }
            return false;
        }

        return $lastService->created_at->diffInMonths(Carbon::now()) >= 12;
    }

    public function daysLeftInWarranty()
    {
        if (!$this->warranty_end_date) {
            return 0;
        }

        $daysLeft = Carbon::now()->diffInDays($this->warranty_end_date, false);
        return max(0, $daysLeft);
    }
}
