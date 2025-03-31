<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'for',
        'issue_date',
        'due_date',
        'amount',
        'payment_status',
        'file_path'
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function service()
    {
        return $this->hasOne(Service::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(User::class, 'client_id');
    }
}