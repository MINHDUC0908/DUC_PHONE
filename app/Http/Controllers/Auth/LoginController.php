<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.login'); 
    }
    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors([
                'email' => 'Tài khoản không tồn tại.'
            ])->withInput();
        }
        if ($user->is_locked == 0) {
            return back()->withErrors([
                'email' => 'Tài khoản của bạn đã bị khóa.'
            ])->withInput();
        }
        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            return back()->withErrors([
                'email' => 'Email hoặc mật khẩu không đúng.'
            ])->withInput();
        }
    
        // Đăng nhập thành công
        return redirect()->route('home')->with("status", "Đăng nhập thành công");
    }
    
    public function logout()
    {
        Auth::logout(); 
        request()->session()->invalidate();
        request()->session()->invalidate();
        return redirect('/login')->with("status", "Đăng xuất thành công"); 
    }
}
