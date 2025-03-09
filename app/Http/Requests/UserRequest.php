<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $userID = $this->route('id') ?? $this->route('user');

        return [
            'name' => 'required|min:2|max:255',
            'email' => 'required|email|min:8|max:255|unique:users,email,' . $userID,
            'password' => $this->isMethod('post') ? 'required|min:8|max:255|confirmed' : 'nullable|min:8|max:255|confirmed', 
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute không được để trống',
            'min' => ':attribute không được nhỏ hơn :min kí tự',
            'max' => ':attribute không được lớn hơn :max kí tự',
            'confirmed' => ':attribute nhập lại không khớp',
            'unique' => ':attribute đã tồn tại trong hệ thống',
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'Họ và tên',
            'email' => 'Email',
            'password' => 'Mật khẩu',
        ];
    }
}
