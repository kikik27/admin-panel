<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Delivery extends Model
{
    protected $fillable = ['name'];

    public $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (Delivery $delivery) {
            $delivery->id = Str::uuid();
        });
    }
}
