<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\Rating;
use Illuminate\Support\Facades\Auth;

class RatingRepository
{
   public function store($data, $customerId)
    {
        $rating = new Rating();
        $rating->customer_id = $customerId;
        $rating->product_id = $data->input("product_id");
        $rating->rating = $data->input("rating");
        $rating->comment = $data->input("comment");

        if ($data->hasFile('image')) {
            $file = $data->file('image');
            $imageName = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path("rating"), $imageName);
            $rating->image = $imageName;
        }

        $rating->save();

        return $rating;
    }

    public function checkPurchased($customer, $productId)
    {
        return Order::where("customer_id", $customer)
                    ->wherehas("orderItems", function ($query) use ($productId) {
                        $query->where("product_id", $productId);
                    })->exists();
    }

    public function getRatingsByProductId($productId)
    {
        return Rating::with('customer')
            ->where('product_id', $productId)
            ->orderBy("created_at", "DESC")
            ->get();
    }
}