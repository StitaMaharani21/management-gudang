<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'thumbnail',
        'about',
        'price',
        'category_id',
        'is_popular',
    ];

    protected $casts = [
        'price' => 'integer',
        'is_popular' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function merchant(){
        return $this->belongsToMany(Merchant::class, 'merchant_products')->withPivot('stock')->withTimestamps();
    }

    public function warehouse(){
        return $this->belongsToMany(Warehouse::class, 'warehouse_products')->withPivot('stock')->withTimestamps();
    }

    public function transaction(){
        return $this->hasMany(TransactionProduct::class);
    }

    public function getMerchantProductStock()
    {
        return $this->merchant()->sum('stock');
    }

    public function getWarehouseProductStock()
    {
        return $this->warehouse()->sum('stock');
    }

    public function getThumbnailUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        return url(Storage::url($value));
    }
}
