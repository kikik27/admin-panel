<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    protected $fillable = ['customer', 'address', 'phone','delivery_id', 'transaction_code'];
    public $keyType = 'string';
    protected $appends = ['amount'];
    public $incrementing = false;

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

    public function TransactionDetails()
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public static function generateTransactionCode()
    {
        $datePrefix = Carbon::now()->format('dmy');
        $lastTransaction = self::where('transaction_code', 'LIKE', "$datePrefix%")
            ->orderBy('transaction_code', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->transaction_code, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $datePrefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT); // Pad with leading zeros
    }

    public function getAmountAttribute()
    {
        return $this->TransactionDetails->sum('amount');
    }
}