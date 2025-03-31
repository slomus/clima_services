<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public function client()
    {
        return $this->belongsTo(User::class);
    }
}