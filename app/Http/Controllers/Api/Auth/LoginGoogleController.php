<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LoginGoogleController extends Controller
{
    public function redirectToGoogle()
    {
        try {
            return response()->json([
                'url' => Socialite::driver('google')
                    ->stateless()
                    ->redirect()
                    ->getTargetUrl()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Không thể kết nối với Google',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function handleGoogleCallback()
    {
        try {
            $customer = Socialite::driver("google")->stateless()->user();
            Log::info('Google OAuth response', ['customer' => $customer]);

            // Tìm theo google_id
            $findCustomer = Customer::where('google_id', $customer->id)->first();

            if (!$findCustomer) {
                // Nếu không có google_id, tìm theo email
                $findCustomer = Customer::where('email', $customer->email)->first();

                if ($findCustomer) {
                    // Gán google_id vào user cũ
                    $findCustomer->update([
                        'google_id' => $customer->id,
                    ]);
                }
            }

            if ($findCustomer) {
                Auth::login($findCustomer);
                $token = $findCustomer->createToken('token')->plainTextToken;
                Log::info('Token Created:', ['token' => $token]);
            } else {
                // Nếu chưa có user nào, tạo mới
                $newCustomer = Customer::create([
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'google_id' => $customer->id,
                    'password' => bcrypt(Str::random(16)),
                ]);
                Auth::login($newCustomer);
                $token = $newCustomer->createToken('token')->plainTextToken;
                Log::info('Token Created for new user:', ['token' => $token]);
            }

            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect()->away($frontendUrl . '/login?token=' . $token);
        } catch (\Exception $e) {
            Log::error('Google login error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect()->away($frontendUrl . '/login?error=' . urlencode($e->getMessage()));
        }
    }
}