<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Shop extends Model
{
    protected $casts = [
        'seller_id' => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($shop) {
            if (empty($shop->slug) && !empty($shop->name)) {
                $baseSlug = Str::slug($shop->name);
                $slug = $baseSlug;
                $counter = 1;
                
                while (Shop::where('slug', $slug)->where('id', '!=', $shop->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter;
                    $counter++;
                }
                
                $shop->slug = $slug;
            }
        });
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'seller_id');
    }

    public function product(){
        return $this->hasMany(Product::class, 'user_id', 'seller_id')->where(['added_by'=>'seller', 'status'=>1]);
    }

    public function scopeActive($query){
        return $query->whereHas('seller', function ($query) {
            $query->where(['status' => 'approved']);
        });
    }
}
