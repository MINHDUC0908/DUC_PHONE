<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;  // Thêm Http

class ShippingAddress extends Model
{
    use HasFactory;

    protected $table = 'shipping_addresses';
    protected $fillable = [
        'customer_id',
        'name',
        'phone',
        'province',
        'district',
        'ward',
        'address',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Lấy tên xã từ ID
    public function getWardNameAttribute()
    {
        if (!$this->ward) {
            return null;
        }

        return Cache::remember("ward_name_{$this->ward}", 60*60, function () {
            try {
                $response = Http::get("https://provinces.open-api.vn/api/w/{$this->ward}?depth=2");  // Sử dụng Http::get
                $data = $response->json();
                return $data['name'] ?? 'Unknown Ward';
            } catch (\Exception $e) {
                Log::error('Error fetching ward name: ' . $e->getMessage());
                return 'Unknown Ward';
            }
        });
    }

    // Lấy tên quận từ ID
    public function getDistrictNameAttribute()
    {
        if (!$this->district) {
            return null;
        }

        return Cache::remember("district_name_{$this->district}", 60 * 60, function () {
            try {
                $response = Http::get("https://provinces.open-api.vn/api/d/{$this->district}?depth=2");  // Sử dụng Http::get
                $data = $response->json();
                return $data['name'] ?? 'Unknown District';
            } catch (\Exception $e) {
                Log::error('Error fetching district name: ' . $e->getMessage());
                return 'Unknown District';
            }
        });
    }

    // Lấy tên tỉnh từ ID
    public function getProvinceNameAttribute()
    {
        if (!$this->province) {
            return null;
        }

        return Cache::remember("province_name_{$this->province}", 60 * 60, function () {
            try {
                $response = Http::get("https://provinces.open-api.vn/api/p/{$this->province}?depth=2");  // Sử dụng Http::get
                $data = $response->json();
                return $data['name'] ?? 'Unknown Province';
            } catch (\Exception $e) {
                Log::error('Error fetching province name: ' . $e->getMessage());
                return 'Unknown Province';
            }
        });
    }
}
