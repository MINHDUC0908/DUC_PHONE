<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'orders';
    protected $fillable = [
        'customer_id',
        'order_number',
        'total_price',
        'status',
        'shipping_address_id',
        "coupon_id",
        "discount_amount"
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address_id');
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }    
    // Mối quan hệ với Coupon
    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    // Tính tổng tiền sau khi áp dụng mã giảm giá
    public function getFinalPrice()
    {
        return $this->total_price - $this->discount_amount;
    }
}
