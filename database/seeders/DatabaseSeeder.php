<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        Category::factory()->count(30)->create();
        // Brand::factory()->count(10)->create();
        // User::factory()->create([
        //     'name' => 'Admin',
        //     'email' => 'Dat@gmail.com',
        //     'password' => Hash::make('12345678', [
        //         'memory' => 1024,    // Tùy chọn bộ nhớ (giới hạn 1024MB)
        //         'time' => 2,         // Thời gian (số vòng lặp)
        //         'threads' => 2       // Số lượng luồng (threads)
        //     ])
        // ]);
        // Customer::factory()->create([
        //     'name' => 'Lê Văn Minh Đức',
        //     'email' => 'duc@gmail.com',
        //     'password' => Hash::make('duc0908@@')
        // ]);
    }
}
