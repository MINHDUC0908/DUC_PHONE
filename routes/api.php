<?php

use App\Http\Controllers\Admin\ChatBotController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LoginGoogleController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\CheckOutController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\ForgotPassword;
use App\Http\Controllers\Api\NewController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\VNPayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Api Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Api routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "Api" middleware group. Enjoy building your Api!
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
// routes/Api.php
Route::get('/products/{productId}/comments', [CommentController::class, 'index']);
Route::get('incrementProduct', [ProductController::class, 'incrementProduct']);
Route::get('ProductNew', [ProductController::class, 'ProductNew']);

Route::get('news', [NewController::class, 'index']);
Route::get('show/{id}', [NewController::class, 'show']);
Route::get('new', [NewController::class, 'New']);
Route::get("limitNew", [NewController::class, "limitNew"]);

Route::get('/auth/google', [LoginGoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [LoginGoogleController::class, 'handleGoogleCallback']);
Route::post('/forgot-password', [ForgotPassword::class, 'ForgotPassword']);
Route::post('/reset-password', [ForgotPassword::class, 'resetPassword']);
Route::get('/searchBrand', [SearchController::class, 'searchBrand']);
Route::get('/searchCategory', [SearchController::class, 'searchCategory']);
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
    Route::post("/customer/image/{id}", [ProfileController::class, "image"]);

    Route::post('/change-password', [ProfileController::class, 'changePassword']);
    Route::post('/send-message-fe', [ChatController::class, 'sendMessageCustomer']);
    Route::get('/message-fe', [ChatController::class, 'index']);

    Route::post("applyCoupon", [CheckOutController::class, "applyCoupon"]);
    Route::post('checkout', [CheckOutController::class,'CheckOut']);


    Route::post("storeReview", [RatingController::class, "storeReview"]);
});
Route::get('rating', [RatingController::class, 'index']);

Route::post('/chatbot', [ChatBotController::class, 'chat']);
