<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderIpRestriction extends Model
{
    protected $fillable = ['ip_address', 'last_order_time'];

    protected $casts = [
        'last_order_time' => 'datetime',
    ];
}
