<?php

use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\NewController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RatingController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Api\VNPayController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\UserController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::group(['middleware' => ['guest']], function(){
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);  
});
Route::get('/', [DashboardController::class, 'dashboard'])->name('home');
Route::group(['middleware' => ['auth', 'role:Admin']], function () {
    Route::resource('user', UserController::class);
    Route::put("toggleLock/{id}", [UserController::class, "toggleLock"])->name("user.toggleLock");
    Route::prefix('product')->name('product.')->group(function(){
        Route::get('/list', [ProductController::class, 'index'])->name('list');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/create', [ProductController::class, 'store'])->name('store');
        Route::get('brands/{category_id}', [ProductController::class, 'getBrandsByCategory']);
        Route::get('edit/{id}', [ProductController::class, 'edit'])->name('edit');
        Route::post('edit/{id}', [ProductController::class, 'update'])->name('update');
        Route::delete('delete/{id}', [ProductController::class, 'destroy'])->name('destroy');
        Route::get('show/{id}', [ProductController::class, 'show'])->name('show');
    });
    Route::resource('colors', ColorController::class);
    Route::prefix('orders')->name('orders.')->group(function(){
        Route::get('list', [OrderController::class, 'order'])->name('list');
        Route::get('show/{id}', [OrderController::class, 'show'])->name('show');
        Route::put('update/{id}', [OrderController::class, 'updateStatus'])->name('updateStatus');
    });
    Route::prefix('customer')->name('customer.')->group(function(){
        Route::get('list', [CustomerController::class,'index'])->name('list');
        Route::put('update/{id}', [CustomerController::class, 'update'])->name('update');
    });
    Route::prefix('brand')->name('brand.')->group(function () {
        Route::get('/list', [BrandController::class, 'index'])->name('list');
        Route::get('/create', [BrandController::class, 'create'])->name('create');
        Route::post('/create', [BrandController::class, 'store'])->name('store');
        Route::get('/show/{id}', [BrandController::class, 'show'])->name('show');
        Route::get('/edit/{id}', [BrandController::class, 'edit'])->name('edit');
        Route::put('/edit/{id}', [BrandController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [BrandController::class, 'destroy'])->name('delete');
    });
    Route::prefix('category')->name('category.')->group(function() {
        Route::get('/list', [CategoryController::class, 'index'])->name('list');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/create', [CategoryController::class, 'store'])->name('store');
        Route::get('edit/{id}', [CategoryController::class, 'edit'])->name('edit');
        Route::post('edit/{id}', [CategoryController::class, 'update'])->name('update');
        Route::delete('delete/{id}', [CategoryController::class, 'destroy'])->name('delete');
    });
    Route::prefix('new')->name('new.')->group(function() {
        Route::get('/list', [NewController::class, 'index'])->name('list');
        Route::get('/create', [NewController::class, 'create'])->name('create');
        Route::post('/create', [NewController::class, 'store'])->name('store');
        Route::post('/show{id}', [NewController::class, 'store'])->name('show');
        Route::get('edit/{id}', [NewController::class, 'edit'])->name('edit');
        Route::put('edit/{id}', [NewController::class, 'update'])->name('update');
        Route::delete('delete/{id}', [NewController::class, 'delete'])->name('destroy');
    });

    // Mã giảm giá
    Route::prefix("coupon")->name("coupon.")->group(function() {
        Route::get("index", [CouponController::class, 'index'])->name("index");
        Route::get("create", [CouponController::class, 'create'])->name("create");
        Route::post("create", [CouponController::class, "store"])->name("store");
        Route::get("edit/{id}", [CouponController::class, 'edit'])->name("edit");
        Route::put("edit/{id}", [CouponController::class, "update"])->name("update");
        Route::delete("delete/{id}", [CouponController::class, "destroy"])->name("destroy");
    });

    Route::prefix("rating")->name("rating.")->group(function() {
        Route::get("index", [RatingController::class, 'index'])->name("index");
        Route::get("ratingImage", [RatingController::class, 'ratingImage'])->name("ratingImage");
        Route::delete('/destroy/{id}', [RatingController::class, 'destroy'])->name('destroy');
    });
    Route::prefix("discount")->name("discount.")->group(function() {
        Route::get("discount", [DiscountController::class, 'index'])->name("index");
        Route::post("store", [DiscountController::class, 'store'])->name("store");
        Route::put("update/{id}", [DiscountController::class, 'update'])->name("update");
        Route::delete("destroy/{id}", [DiscountController::class, 'destroy'])->name("destroy");
    });
});
Route::prefix('profile')->name('profile.')->group(function() {
    Route::get("index", [ProfileController::class, 'index'])->name('index'); 
    Route::put("update/{id}", [ProfileController::class, 'update'])->name('update');
    Route::put("image/{id}", [ProfileController::class, "image"])->name('image');
    Route::delete("image/{id}", [ProfileController::class, "deleteImage"])->name('deleteImage');
    Route::put("updatePassword/{id}", [ProfileController::class, 'updatePassword'])->name("updatePassword");
});

// Comment
Route::prefix("comment")->name("comment.")->group(function() {
    Route::get("admin/list/comment", [CommentController::class, 'index'])->name("list");
    Route::post('/admin/comment/reply/{commentId}', [CommentController::class, 'reply'])->name('reply');
    Route::delete('/{id}', [CommentController::class, 'destroy'])->name('delete');
});

// Xử lý gửi tin nhắn từ form
Route::post('/admin/send-message/{id}', [ChatController::class, 'sendMessageUser'])->name('admin.send-message');
Route::get('/admin/seen-message/{id}', [ChatController::class, 'show'])->name('admin.seen-message');
Route::get("/admin/seen-index", [ChatController::class, "index"])->name("admin.seen-index");
Route::get('logout', [LoginController::class, 'logout'])->name('logout');
Route::get('phan-vai-tro/{id}', [UserController::class, 'phanvaitro'])->name('phan-vai-tro');
Route::post('storeRole/{id}', [UserController::class, 'storeRole'])->name('storeRole');
Route::get('phan-quyen/{id}', [UserController::class, 'phanquyen'])->name('phan-quyen');
Route::post('storePermission/{id}', [UserController::class, 'storePermission'])->name('storePermission');
Route::get('add-role', [UserController::class, 'createRole'])->name('add-quyen');
Route::post('storeRoles', [UserController::class, 'storeRoles'])->name('storeRoles');
Route::get('add-permissions', [UserController::class, 'createPermission'])->name('add-permission');
Route::post('Permissions', [UserController::class, 'Permissions'])->name('Permission');
Route::get('/api/statistics/monthly-revenue', [StatisticsController::class, 'monthlyRevenue']);
Route::get('/api/statistics/top-products', [StatisticsController::class, 'topSellingProducts']);
Route::get('/api/statistics/orderStatusStats', [StatisticsController::class, 'orderStatusStats']);
Route::get('/api/statistics/weeklyRevenueStats', [StatisticsController::class, 'weeklyRevenueStats']);
Route::get('/api/statistics/dailyRevenueStats', [StatisticsController::class, 'dailyRevenueStats']);


Route::get('/vnpay-return', [VNPayController::class, 'vnpayReturn'])->name('vnpay.return');
