<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_address_id' => 'required|exists:shipping_addresses,id',
            'coupon' => 'nullable|string|exists:coupons,code'
        ];
    }
}
