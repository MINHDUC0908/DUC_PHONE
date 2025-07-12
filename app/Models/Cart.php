<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'status'];
    protected $appends = ['total_price'];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function cartItems()
    {
        return $this->hasMany(CartItem::class)->orderBy('id', 'DESC');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function color()
    {
        return $this->belongsTo(Color::class, 'color_id');
    }
    // Tính tổng tiền của các cartItem có selected = 1
    public function getTotalPriceAttribute()
    {
        return $this->cartItems
            ->where('selected', 1)    // chỉ lấy những item đã chọn
            ->sum('total_price');     // sum thông qua accessor bên CartItem
    }
}
