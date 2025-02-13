<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    protected $fillable = ['color', 'product_id', 'quantity'];

    public function products()
    {
        return $this->belongsTo(Product::class, 'product_id'); // Chỉ định khóa ngoại đúng
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
