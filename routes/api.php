<?php

use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\auth\LoginController;
use App\Http\Controllers\api\auth\RegisterController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\api\CommentController;
use App\Http\Controllers\Api\NewController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\api\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
// Routes cho Category
Route::get('/categories', [CategoryController::class, 'index'])->name('indexCategory');
Route::get('/categories/{id}', [CategoryController::class, 'show'])->name('showCategory');

// Routes cho Brand
Route::get('/brands', [BrandController::class, 'index'])->name('indexBrand');
Route::get('/brands/{id}', [BrandController::class, 'show'])->name('showBrand');
Route::get('product', [ProductController::class, 'index'])->name('Product');
Route::get('category/{id}/product', [CategoryController::class, 'getProductCategory']);
Route::get('brand/{id}/product', [BrandController::class, 'getProductBrand']);
Route::get('product/{id}', [ProductController::class, 'show']);
Route::post('login', [LoginController::class, 'login']);
Route::middleware('auth:sanctum')->get('/user-profile', [LoginController::class, 'getUserProfile']);
Route::post('register', [RegisterController::class, 'register']);
Route::post('logout', [LoginController::class, 'logout']);
// routes/api.php
Route::get('/products/{productId}/comments', [CommentController::class, 'index']);
Route::get('incrementProduct', [ProductController::class, 'incrementProduct']);
Route::get('ProductNew', [ProductController::class, 'ProductNew']);

Route::get('news', [NewController::class, 'index']);
Route::get('show/{id}', [NewController::class, 'show']);
Route::get('new', [NewController::class, 'New']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('storeCart', [CartController::class, 'storeCart']);
    Route::get('countCart' , [CartController::class, 'countCart']);
    Route::get('cart', [CartController::class, 'viewCart']);
    Route::get('viewCartPayment', [CartController::class, 'viewCartPayment']);
    Route::delete('delete/{id}', [CartController::class, 'delete']);
    Route::delete('deleteAll/{id}', [CartController::class, 'deleteAll']);
    Route::post('update/{id}', [CartController::class, 'update']);
    Route::post('createAddress', [AddressController::class, 'store']);
    Route::get('address', [AddressController::class, 'index']);
    Route::post('checkout', [OrderController::class,'CheckOut']);
    Route::get('order', [OrderController::class,'order']);
    Route::put('updateOrderStatus/{id}', [OrderController::class, 'cancelOrder']);
    Route::put('updateOrder/{id}', [OrderController::class, 'updateOrder']);
    Route::put('updateQuantity/{id}', [OrderController::class, 'updateQuantity']);
    Route::put('updateAllOrders', [OrderController::class, 'updateAllOrders']);
    Route::get('/orders/status/{status}', [OrderController::class, 'getOrdersByStatus']); 
    Route::get('has_been_deleted', [OrderController::class, 'has_been_deleted']);
    Route::get('showOrder/{id}', [OrderController::class, 'showOrder']);

    Route::post('/products/{productId}/comments', [CommentController::class, 'store']);  
    
    

    Route::put('/customer/update/{id}', [ProfileController::class, 'update']);


    Route::post('/change-password', [ProfileController::class, 'changePassword']);
    Route::post('/send-message-fe', [ChatController::class, 'sendMessage']);
    Route::get('/message-fe', [ChatController::class, 'index']);
});