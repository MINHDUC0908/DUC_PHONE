<?php

namespace App\Http\Controllers\Api\Auth;

use App\Events\CustomerLoggedIn;
use App\Events\CustomerLoggedOut;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
            ], 422);
        }

        $customer = Customer::where('email', $request->email)->first();
        if (!$customer || $customer->selected == 0)
        {
            return response()->json([
                'message' => 'Tài khoản của bạn đã bị khóa hoặc không tồn tại',
            ], 401);
        }
        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return response()->json([
                'message' => 'Thông tin đăng nhập không chính xác.',
            ], 401);
        }

        $token = $customer->createToken('CustomerToken')->plainTextToken;
        event(new CustomerLoggedIn($customer));
        return response()->json([
            'message' => 'Đăng nhập thành công!',
            'token' => $token,
            'user' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
            ],
        ], 200);
    }
    public function logout(Request $request)
    {
        // Kiểm tra nếu người dùng đang sử dụng Sanctum guard
        if (Auth::guard('sanctum')->check()) {
            // Lấy user từ Sanctum guard
            $user = Auth::guard('sanctum')->user();
            
            // Gọi sự kiện khi người dùng đăng xuất
            event(new CustomerLoggedOut($user));

            // Xóa tất cả các token của người dùng
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'Đăng xuất thành công.'
            ], 200);
        }

        // Nếu người dùng đăng nhập bằng guard mặc định (web hoặc khác)
        $user = Auth::user();

        if ($user) {
            // Gọi sự kiện khi người dùng đăng xuất
            event(new CustomerLoggedOut($user));

            // Xóa tất cả các token của người dùng
            $user->tokens->each(function ($token) {
                $token->delete();
            });
        }

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công.'
        ], 200);
    }
    public function getUserProfile(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        return response()->json($user);
    }
}
