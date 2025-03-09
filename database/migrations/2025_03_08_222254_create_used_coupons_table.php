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
    public function up()
    {
        Schema::create('used_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id'); // Người dùng nào đã dùng
            $table->unsignedBigInteger('coupon_id'); // Mã giảm giá nào
            $table->timestamps();

            // Ràng buộc khóa ngoại
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('coupon_id')->references('id')->on('coupons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('used_coupons');
    }
};
