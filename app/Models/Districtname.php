<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Districtname extends Model
{
    use HasFactory;
    protected $fillable=[
        'district_name_en',
        'district_name_bn',
        'district_shipping_charge',
        'status',
        'others'
    ];
}
