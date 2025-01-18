<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class catalogue extends Model
{
    protected $fillable = ['product_id', 'image'];

    public $incrementing = false;

    public $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(callback: function (catalogue $catalogue) {
            $catalogue->id = Str::uuid();
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}