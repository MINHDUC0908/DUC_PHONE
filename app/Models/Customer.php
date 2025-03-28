<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    use HasFactory, HasApiTokens;
    protected $table = 'customers';
    protected $fillable = [
        "google_id",
        'name',
        'email',
        'password',
        'status',
        "image",
        "gender"
    ];

    protected $hidden = [
        'password',
    ];
    public function cart()
    {
        return $this->hasOne(Cart::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    public function ratings() {
        return $this->hasMany(Rating::class);
    }
}
