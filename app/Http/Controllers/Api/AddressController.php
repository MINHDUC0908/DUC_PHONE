<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use App\Services\AddressService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    protected $addressService;
    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }
    public function index()
    {
        try {
            $customer = Auth::id();

            if (!$customer) {
                return response()->json([
                    'error' => 'Người dùng chưa đăng nhập',
                    'message' => 'Vui lòng đăng nhập để xem giỏ hàng',
                ]);
            }

            // $addresses = ShippingAddress::where('customer_id', $customer)->get();
            // foreach ($addresses as $address) {
            //     $address->province_name = $address->province_name;
            //     $address->district_name = $address->district_name;
            //     $address->ward_name = $address->ward_name;
            // }
            // return response()->json([
            //     'message' => $addresses->isEmpty() ? 'Chưa có địa chỉ' : 'Địa chỉ của bạn',
            //     'data' => $addresses,
            // ]);

            return $this->addressService->getAddresses();
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Đã xảy ra lỗi',
                'message' => 'Không thể lấy dữ liệu, vui lòng thử lại sau.',
            ], 500);
        }
    }
    public function store(Request $request)
    {
        try {
            // $customer = Auth::id();
            // if (!$customer) {
            //     return response()->json(['message' => 'Người dùng chưa đăng nhập'], 401);
            // }
            // // Xác thực dữ liệu đầu vào
            // $validatedData = $request->validate([
            //     'name' => 'required|string|max:255',
            //     'phone' => 'required|regex:/^[0-9]{10,11}$/',
            //     'province' => 'required|max:255',
            //     'district' => 'required|max:255',
            //     'ward' => 'required|max:255',
            //     'address' => 'required|string|max:500',
            // ]);
            // // Lưu thông tin vào bảng shipping_addresses
            // $shippingAddress = ShippingAddress::create([
            //     'customer_id' => $customer,
            //     'name' => $validatedData['name'],
            //     'phone' => $validatedData['phone'],
            //     'province' => $validatedData['province'],
            //     'district' => $validatedData['district'],
            //     'ward' => $validatedData['ward'],
            //     'address' => $validatedData['address'],
            // ]);
            // return response()->json([
            //     'status' => true,
            //     'message' => 'Địa chỉ giao hàng đã được lưu thành công!',
            //     'data' => $shippingAddress
            // ], 201);
            return $this->addressService->store($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Đã xảy ra lỗi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
