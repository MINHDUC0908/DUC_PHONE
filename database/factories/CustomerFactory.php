<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class CustomerFactory extends Factory
{
    /**
     * Định nghĩa trạng thái mặc định của mô hình.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(), 
            'password' => Hash::make('password'),
        ];
    }
}
