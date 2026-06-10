<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class PendingCheckout extends Model
{
    protected $fillable = [
        'customer_type',
        'customer_id',
        'guest_id',
        'contact_person_name',
        'phone',
        'email',
        'shipping_address',
        'city',
        'thana',
        'zip',
        'country',
        'billing_address',
        'order_comment',
        'total_amount',
        'cart_items',
        'status',
        'order_id',
        'paid_at',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'total_amount' => 'float',
        'order_id' => 'integer',
        'paid_at' => 'datetime',
        'cart_items' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
