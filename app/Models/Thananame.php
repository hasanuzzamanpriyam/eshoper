<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thananame extends Model
{
    use HasFactory;
    protected $fillable=[
        'dist_id',
        'thana_name_en',
        'thana_name_bn',
        'thana_shipping_charge',
        'status',
        'district_name_eng',
        'district_name_bangla',
        'others'
    ];
}
