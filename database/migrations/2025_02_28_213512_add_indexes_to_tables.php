<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() :void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->index('category_name');
        });

        // Thêm index vào bảng brands
        Schema::table('brands', function (Blueprint $table) {
            $table->index('brand_name');
            $table->index('category_id');
        });

        // Thêm index vào bảng products
        Schema::table('products', function (Blueprint $table) {
            $table->index('category_id');
            $table->index('brand_id');
            $table->index('product_name');
        });

        // Thêm index vào bảng colors
        Schema::table('colors', function (Blueprint $table) {
            $table->index('product_id');
        });

        // Thêm index vào bảng carts
        Schema::table('carts', function (Blueprint $table) {
            $table->index('customer_id');
        });

        // Thêm index vào bảng orders
        Schema::table('orders', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('shipping_address_id');
            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};
