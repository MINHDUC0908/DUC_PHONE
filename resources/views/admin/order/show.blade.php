@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Chi tiết đơn hàng #{{ $order->order_number }}</h2>
        
        <div class="card mt-4">
            <div class="card-header">
                <h4>Thông tin đơn hàng</h4>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tr>
                        <th>Mã đơn hàng</th>
                        <td>{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <th>Khách hàng</th>
                        <td>{{ $order->customer->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $order->customer->email }}</td>
                    </tr>
                    <tr>
                        <th>Ngày tạo</th>
                        <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Trạng thái</th>
                        <td>{{ $order->status }}</td>
                    </tr>
                    <tr>
                        <th>Tổng giá</th>
                        <td>{{ number_format($order->total_price, 2) }} VND</td>
                    </tr>
                    <tr>
                        <th>Địa chỉ giao hàng</th>
                        <td>{{ $order->shippingAddress->address}}, {{$order->shippingAddress->ward_name}}, {{$order->shippingAddress->district_name}}, {{$order->shippingAddress->province_name}}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h4>Danh sách sản phẩm</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Hình ảnh sản phẩm</th>
                            <th>Tên sản phẩm</th>
                            <th>Màu sắc</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Tổng giá</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td><img src="{{ asset('imgProduct/' . $item->product->images) }}" alt="Image" style="width: 100px; height: auto;"></td>
                                <td>{{ Str::limit($item->product->product_name, 50) }}</td>
                                <td>{{ !empty($item->color->color) ? $item->color->color : 'None' }}</td>
                                <td>{{ number_format($item->price, 2) }} ₫</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ number_format($item->price * $item->quantity, 2) }} ₫</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('orders.list') }}" class="btn btn-primary">Quay lại danh sách đơn hàng</a>
        </div>
    </div>
@endsection
