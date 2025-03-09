<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword as MailForgotPassword;
use App\Models\Customer;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPassword extends Controller
{
    public function ForgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);
            $email = $request->input('email');
            // Kiểm tra email có tồn tại không
            $customer = Customer::where("email", $email)->first();
            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email không tồn tại trong hệ thống',
                ], 404);
            }
            // Tạo token reset password
            $token = Str::random(60);

            // Xóa token cũ nếu có
            DB::table('password_resets')->where('email', $email)->delete();

            // Lưu token vào database
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
            // Gửi email đặt lại mật khẩu
            Mail::to($email)->send(new MailForgotPassword($token));

            return response()->json([
                'status' => "success",
                'message' => 'Chúng tôi đã gửi liên kết đặt lại mật khẩu đến email của bạn!',
            ], 200);
        } catch (Exception $e) {
            Log::error('Lỗi quên mật khẩu: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => "Lỗi khi gửi yêu cầu quên mật khẩu",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);
        $tokenData = DB::table('password_resets')->where('token', $request->token)->first();
        if (!$tokenData) {
            return response()->json([
                'message' => 'Token không hợp lệ hoặc đã hết hạn.'
            ], 400);
        }
        // Tìm user theo email
        $customer = Customer::where('email', $tokenData->email)->first();
        if (!$customer) {
            return response()->json([
                'message' => 'Email không tồn tại.'
            ], 400);
        }
        Log::debug($customer);
        // Cập nhật mật khẩu mới
        Log::debug('Mật khẩu trước:', ['password' => $customer->password]);

        $customer->password = Hash::make($request->password);
        $customer->save();        
        Log::debug('Mật khẩu sau:', ['password' => $customer->password]);
        // Xóa token sau khi sử dụng
        DB::table('password_resets')->where('email', $tokenData->email)->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Mật khẩu đã được đặt lại thành công.'
        ]);
    }
}
