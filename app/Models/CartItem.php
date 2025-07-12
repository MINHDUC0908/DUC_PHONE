<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = ['cart_id', 'product_id', 'color_id', 'quantity', 'price', 'selected'];

    protected $appends = ['total_price'];
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }    
    public function colors()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }    
    
    public function getTotalPriceAttribute()
    {
        if ($this->selected && $this->product) {
            return $this->product->getDiscountedPrice() * $this->quantity;
        }

        return 0;
    }
}
