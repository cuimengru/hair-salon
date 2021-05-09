<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'description', 'price', 'stock','product_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
