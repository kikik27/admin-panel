<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    protected $fillable = ['customer_id', 'delivery_id', 'amount', 'status'];
    protected $keyType = 'string';

    protected static function booted(): void
    {
        static::creating(function (Transaction $transaction) {
            $transaction->id = Str::uuid();
        });
    }

    public function Delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    public function Customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function TransactionDetails(){
        return $this->hasMany(TransactionDetail::class);
    }
}