<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'brand_id', 'product_name', 'description', 'price', 'outstanding', 'images', 'description_image', "thumbnail"
    ];
    protected $appends = ['time_left', "discounted_price"];
    public function getImageUrlAttribute()
    {
        return asset($this->images);
    }

    public function getDescriptionImagesUrlAttribute()
    {
        $images = json_decode($this->description_image, true);
        if ($images) {
            return array_map(fn($img) => asset($img), $images);
        }
        return [];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function colors()
    {
        return $this->hasMany(Color::class, 'product_id'); // Chỉ định khóa ngoại đúng
    }
    

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
    public function ratings() {
        return $this->hasMany(Rating::class, 'product_id');
    }
    
    public function averageRating() {
        return $this->ratings()->avg('rating') ?? 0;
    }
    public function discount()
    {
        return $this->hasOne(Discount::class);
    }    
    public function getTimeLeftAttribute()
    {
        if ($this->discount && $this->discount->end_date) {
            $end = \Carbon\Carbon::parse($this->discount->end_date);
            $now = \Carbon\Carbon::now();
    
            if ($end->isPast()) {
                return 'Đã hết hạn';
            }
    
            // Tính toán thời gian còn lại
            $diff = $end->diff($now);
            $days = $diff->d > 0 ? $diff->d . ' ngày ' : '';
            $hours = $diff->h > 0 ? $diff->h . ' giờ ' : '';
            $minutes = $diff->i > 0 ? $diff->i . ' phút' : '';
    
            return $days . $hours . $minutes;
        }
        return 'Không có giảm giá';
    }

    public function getDiscountedPrice()
    {
        if ($this->discount && $this->discount->status) {
            $currentDate = now();

            if (
                (!$this->discount->start_date || $this->discount->start_date <= $currentDate) &&
                (!$this->discount->end_date || $this->discount->end_date >= $currentDate)
            ) {
                if ($this->discount->discount_type === 'percent') {
                    $discountAmount = $this->price * ($this->discount->discount_value / 100);
                } else {
                    $discountAmount = $this->discount->discount_value;
                }

                return max($this->price - $discountAmount, 0);
            }
        }

        return $this->price;
    }

    public function getDiscountedPriceAttribute()
    {
        return $this->getDiscountedPrice(); // gọi lại logic sẵn có
    }
}
