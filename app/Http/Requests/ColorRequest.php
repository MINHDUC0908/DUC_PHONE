<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ColorRequest extends FormRequest
{
   public function authorize()
   {
       return true;
   }

   public function rules()
   {
       return [
           'color' => '',
        //    'product_id' => [
        //         'required',
        //         'exists:products,id',
        //         Rule::unique('colors')->where(function ($query) {
        //             return $query->where('color', $this->color)
        //                         ->where('product_id', $this->product_id);
        //         })
        //     ],
           'quantity' => 'required|integer|min:0'
       ];
   }

   public function messages()
   {
       return [
        //    'product_id.required' => 'Sản phẩm không được để trống',
        //    'product_id.exists' => 'Sản phẩm không tồn tại',
        //    'product_id.unique' => 'Màu sắc này đã tồn tại cho sản phẩm',
        //    'quantity.required' => 'Số lượng không được để trống',
           'quantity.integer' => 'Số lượng phải là số nguyên',
           'quantity.min' => 'Số lượng không được nhỏ hơn 0'
       ];
   }
}