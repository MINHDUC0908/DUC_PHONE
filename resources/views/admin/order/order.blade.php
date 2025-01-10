@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Quản lý đơn hàng</h2>
        
        <!-- Dropdown để lọc theo trạng thái -->
        <form action="{{ route('orders.list') }}" method="GET">
            <label for="status">Chọn trạng thái: </label>
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="Waiting for confirmation" {{ $status == 'Waiting for confirmation' ? 'selected' : '' }}>Đang chờ xác nhận</option>
                <option value="Processing" {{ $status == 'Processing' ? 'selected' : '' }}>Đang xử lí</option>
                <option value="Delivering" {{ $status == 'Delivering' ? 'selected' : '' }}>Đang vận chuyển</option>
                <option value="Completed" {{ $status == 'Completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                <option value="Cancel" {{ $status == 'Cancel' ? 'selected' : '' }}>Đã hủy</option>
            </select>
        </form>

        <table class="table table-bordered mt-4" id="myTable1">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Mã đơn hàng</th>
                    <th>Khách hàng</th>
                    <th>Tổng giá</th>
                    <th>Trạng thái</th>
                    <th>Địa chỉ giao hàng</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->customer->name }}</td>
                        <td>{{ number_format($order->total_price, 2) }} ₫</td>
                        <td>
                            <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <!-- Hiển thị trạng thái tiếp theo -->
                                @if ($order->status == 'Waiting for confirmation')
                                    <button type="submit" name="status" value="Processing" class="btn btn-warning btn-sm">Đang chờ xác nhận</button>
                                @elseif ($order->status == 'Processing')
                                    <button type="submit" name="status" value="Delivering" class="btn btn-info btn-sm">Đang xử lí</button>
                                @elseif ($order->status == 'Delivering')
                                    <button type="submit" name="status" value="Completed" class="btn btn-success btn-sm">Đang vận chuyển</button>
                                @elseif ($order->status == 'Completed')
                                    <!-- Không hiển thị nút gì khi đã hoàn thành -->
                                    <button type="button" class="btn btn-secondary btn-sm" disabled>Đã hoàn thành</button>
                                @elseif ($order->status == 'Cancel')
                                    <!-- Không hiển thị nút gì khi đã hủy -->
                                    <button type="button" class="btn btn-secondary btn-sm" disabled>Đã hủy</button>
                                @endif
                            </form>
                        </td>                                            
                        <td>{{ $order->shippingAddress->address }}</td>
                        <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                        <td>
                            <a href="{{route('orders.show', $order->id)}}" class="btn btn-danger btn-sm">
                                <i class="fas fa-eye"></i> Xem chi tiết
                            </a>
                        </td>                                         
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
