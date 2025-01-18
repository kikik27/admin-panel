<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'description', 'price', 'product_code', 'category'];

    protected $keyType = 'string';

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $product->id = Str::uuid();
        });
    }

    public function catalogImages()
    {
        return $this->hasMany(Catalogue::class, 'product_id');
    }
}