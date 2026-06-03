<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CustomerReputation extends Model
{
    protected $fillable = ['phone', 'data'];

    protected $casts = [
        'data' => 'array',
    ];
}
