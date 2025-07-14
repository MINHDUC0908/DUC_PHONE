<?php

namespace App\Services;

use App\Repositories\AddressRepository;
use Illuminate\Support\Facades\Auth;

class AddressService
{
    protected $addressRepo;
    public function __construct(AddressRepository $addressRepo)
    {
        $this->addressRepo = $addressRepo;
    }

    public function store($request)
    {
        $customerId = Auth::id();

        $this->addressRepo->store(
            $request->name,
            $request->phone,
            $request->province,
            $request->district,
            $request->ward,
            $request->address,
            $customerId
        );
        
        return response()->json([
            'status' => true,
            'message' => 'Địa chỉ giao hàng đã được lưu thành công!',
        ], 201);
    }

    public function getAddresses()
    {
        $customerId = Auth::id();
        $data = $this->addressRepo->getAddresses($customerId);
        return response()->json([
            'status' => true,
            'message' => $data->isEmpty() ? 'Chưa có địa chỉ' : 'Địa chỉ của bạn',
            'data' => $data,
        ]);
    }
}