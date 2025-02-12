<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'brand_id', 'product_name', 'description', 'price', 'outstanding', 'images', 'description_image',
    ];

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
        return $this->hasMany(Color::class, 'product_id'); // Chỉ định khóa ngoại
    }
    

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }
}
