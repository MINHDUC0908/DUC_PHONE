<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id(); // Khóa chính
            $table->unsignedBigInteger('cart_id'); 
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('color_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 2);
            $table->integer('selected')->default(1);
            $table->timestamps(); 
            $table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cart_items');
    }
};
