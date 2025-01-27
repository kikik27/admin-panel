<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class TransactionDetail extends Model
{
    protected $fillable = ['transaction_id', 'product_id', 'qty', 'amount'];
    public $keyType = 'string';
    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (TransactionDetail $transactionDetail) {
            $transactionDetail->id = Str::uuid();
        });
    }

    public function Transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function Product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
