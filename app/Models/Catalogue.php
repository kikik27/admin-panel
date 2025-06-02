<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Catalogue extends Model
{
  protected $fillable = ['product_id', 'image'];

  public $incrementing = false;

  public $keyType = 'string';

  protected static function booted(): void
  {
    static::creating(function (Catalogue $catalogue) {
      $catalogue->id = Str::uuid();
    });
  }

  public function product()
  {
    return $this->belongsTo(Product::class, 'product_id');
  }

  // Accessor for full image URL
  public function getImageUrlAttribute()
  {
    return $this->image ? url('storage/' . $this->image) : null;
  }

  // Pastikan image_url bisa di-akses saat model di-serialize
  protected $appends = ['image_url'];
}
