@extends('admin.layouts.app')

@section('content')
    <div class="container mt-4">
        <h2>Danh sách sản phẩm</h2>
        
        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <a href="{{ route('product.create') }}" class="btn btn-success mb-3">
            <i class="fas fa-plus-circle"></i> Thêm sản phẩm mới
        </a>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered table-striped" id="myTable2">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Danh mục</th>
                            <th>Thương hiệu</th>
                            <th>Ảnh</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->product_name }}</td>
                                <td>{{ number_format($product->price, 0, ',', '.') }}₫</td>
                                <td>{{ $product->category->category_name }}</td>
                                <td>{{ $product->brand->brand_name }}</td>
                                <td>
                                    @if($product->images)
                                        <img src="{{ asset('imgProduct/' . $product->images) }}" alt="Image" style="width: 100px; height: auto;">
                                    @else
                                        <span class="text-muted">Không có ảnh</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-start">
                                        <a href="{{ route('product.show', ['id' => $product->id]) }}" class="btn btn-info btn-sm mr-2" title="Xem chi tiết" style="margin-right: 5px;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('product.edit', ['id' => $product->id]) }}" class="btn btn-warning btn-sm mr-2" title="Chỉnh sửa" style="margin-right: 5px;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('product.destroy', ['id' => $product->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
