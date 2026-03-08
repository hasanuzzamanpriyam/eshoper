<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryCharge extends Model
{
    use HasFactory;

    protected $table = 'delivery_charges';

    protected $fillable = [
        'local_delivery_charge',
        'country_delivery_charge',
    ];
}
