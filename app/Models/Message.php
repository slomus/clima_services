<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public function sender()
    {
        return $this->belongsTo(User::class, 'from');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'to');
    }
}