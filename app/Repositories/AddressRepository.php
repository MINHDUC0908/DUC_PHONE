<?php

namespace App\Repositories;

use App\Models\ShippingAddress;

class AddressRepository
{
    public function store($name, $phone, $province, $district, $ward, $address, $customerId)
    {
        // Kiểm tra xem người dùng đã đăng nhập hay chưa
        if (!$customerId) {
            throw new \Exception('Người dùng chưa đăng nhập');
        }
        // Lưu thông tin địa chỉ vào bảng shipping_addresses
        ShippingAddress::create([
            'customer_id'   => $customerId,
            'name'          => $name,
            'phone'         => $phone,
            'province'      => $province, // <-- dòng này đang bị thiếu
            'district'      => $district,
            'ward'          => $ward,
            'address'       => $address,
        ]);
    }


    public function getAddresses($customerId)
    {
        // Lấy tất cả địa chỉ giao hàng của người dùng
        $addresses =  ShippingAddress::where('customer_id', $customerId)->get();
        foreach ($addresses as $address) {
            $address->province_name = $address->province_name;
            $address->district_name = $address->district_name;
            $address->ward_name = $address->ward_name;
        }

        return $addresses;
    }
}