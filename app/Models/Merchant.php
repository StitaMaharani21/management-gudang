<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Merchant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'photo',
        'address',
        'phone',
        'keeper_id',
    ];

    public function keeper()
    {
        return $this->belongsTo(User::class, 'keeper_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'merchant_products')->withPivot('stock')->withTimestamps();
    }

    public function transactions(){
        return $this->hasMany(Transaction::class);
    }

    public function getPhotoUrlAttribute($value)
    {
        if (!$value) {
            return null;
        }

        return url(Storage::url($value));
    }
}