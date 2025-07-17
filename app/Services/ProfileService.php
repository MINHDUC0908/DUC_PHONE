<?php

namespace App\Services;
use App\Repositories\ProfileRepository;
use Illuminate\Support\Facades\Log;

class ProfileService
{
    protected $profileRepository;

    public function __construct(ProfileRepository $profileRepository)
    {
        $this->profileRepository = $profileRepository;
    }


    public function updateProfile($request, $id)
    {
        $customer = $this->profileRepository->findByIdCustomer($id);
        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }
        $customer->name = $request->input('name');
        $customer->date = $request->input('date');
        $customer->gender = $request->gender ? 1 : 0;
        $this->profileRepository->save($customer);

        return response()->json([
            'status' => true,
            'message' => 'Cập nhật thành công',
            'data' => $customer,
        ], 200);
    }

    public function image($request, $id)
    {
        $customer = $this->profileRepository->findByIdCustomer($id);
        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        if ($request->hasFile('image')) {
            // $file = $request->file('image');
            // // ✅ Xóa ảnh cũ nếu có
            // if (!empty($customer->image)) {
            //     Storage::delete("public/imgCustomer/{$customer->image}");
            // }
            // // ✅ Tạo tên file mới & lưu vào thư mục
            // $filename = time() . '.' . $file->getClientOriginalExtension();
            // $file->storeAs('public/imgCustomer', $filename);
            if ($customer->image) {
                $imagePath = public_path('imgCustomer/' . $customer->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $image = $request->file('image');
            $filename = time() . " - " . $image->getClientOriginalName();
            $image->move(public_path("imgCustomer"), $filename);
            $customer->image = $filename;
            $this->profileRepository->save($customer);

            return response()->json([
                'message' => 'Cập nhật ảnh thành công!',
                'image' => asset("imgCustomer/{$filename}"),
                'status' => "success",
            ]);
        }
    }
}