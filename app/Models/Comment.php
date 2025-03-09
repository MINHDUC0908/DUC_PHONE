<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'customer_id',
        'admin_id',
        'parent_id',
        'content',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, "admin_id");
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('replies.customer', 'replies.admin', 'replies.replies'); 
    }
    

    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }
}
