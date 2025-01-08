<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = ['name', 'price', 'description', 'price', 'image'];

    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $product->id = Str::uuid();
        });
    }
}
