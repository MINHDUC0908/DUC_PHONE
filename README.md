<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).



1. has
Ý nghĩa
Kiểm tra xem một model có ít nhất một bản ghi liên quan trong quan hệ hay không.
Không áp dụng điều kiện cụ thể lên bảng liên quan, chỉ kiểm tra sự tồn tại.

Cú pháp
Model::has('relationship')->get();

Ví dụ
Giả sử bạn có model Order với quan hệ hasMany tới OrderItem:
$orders = Order::has('orderItems')->get();
Trả về tất cả các đơn hàng (Order) có ít nhất một mục đơn hàng (OrderItem).


2. whereHas
Ý nghĩa
Tương tự has, nhưng cho phép thêm điều kiện lọc trên bảng liên quan.
Dùng khi bạn cần kiểm tra sự tồn tại của bản ghi liên quan thỏa mãn một điều kiện cụ thể.

Cú pháp
Model::whereHas('relationship', function ($query) {
    $query->where('column', 'value');
})->get();

$hasPurchased = Order::where('customer_id', $customer)
    ->whereHas('orderItems', function ($query) use ($request) {
        $query->where('product_id', $request->input("product_id"));
    })
    ->exists();

Kiểm tra xem khách hàng $customer có đơn hàng nào chứa sản phẩm với product_id từ request không.

Nâng cao: Kết hợp nhiều điều kiện
$orders = Order::where('customer_id', $customer)
    ->whereHas('orderItems', function ($query) use ($request) {
        $query->where('product_id', $request->input("product_id"))
              ->where('quantity', '>', 1);
    })
    ->get();
    Trả về các đơn hàng mà khách hàng đã mua sản phẩm cụ thể với số lượng lớn hơn 1.


    3. doesntHave
Ý nghĩa
Ngược lại với has: Kiểm tra xem một model không có bản ghi liên quan nào.

Cú pháp
Model::doesntHave('relationship')->get();

$emptyOrders = Order::doesntHave('orderItems')->get();
Trả về tất cả các đơn hàng không có mục đơn hàng nào.

4. whereDoesntHave
Ý nghĩa
Ngược lại với whereHas: Kiểm tra xem một model không có bản ghi liên quan nào thỏa mãn điều kiện cụ thể.
Cú pháp

Model::whereDoesntHave('relationship', function ($query) {
    $query->where('column', 'value');
})->get();


$orders = Order::where('customer_id', $customer)
    ->whereDoesntHave('orderItems', function ($query) use ($request) {
        $query->where('product_id', $request->input("product_id"));
    })
    ->get();

    Trả về các đơn hàng của khách hàng không chứa sản phẩm với product_id từ request.
    
    SELECT * FROM orders
        WHERE customer_id = ?
        AND NOT EXISTS (
            SELECT 1 FROM order_items
            WHERE order_items.order_id = orders.id
            AND product_id = ?
        );


## 📸 Giao diện Demo

![Demo](public/demo/image.png)