@extends('admin.layouts.app')

@section('content')
    <style>
        .truncate-3-lines {
            display: -webkit-box;
            -webkit-box-orient: vertical;
            overflow: hidden;
            -webkit-line-clamp: 5; /* Giới hạn số dòng */
            line-clamp: 3; /* Cho trình duyệt hiện đại */
            text-overflow: ellipsis; /* Hiển thị dấu "..." khi bị cắt */
        }
    </style>
    <div class="container mt-4">
        <h2>Danh sách bài viết</h2>
        
        @if(session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <a href="{{ route('new.create') }}" class="btn btn-success mb-3">
            <i class="fas fa-plus-circle"></i> Thêm bài viết mới
        </a>

        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên bài viết/th>
                            <th>Mô tả bài viết</th>
                            <th>Hình ảnh </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($news as $new)
                            <tr>
                                <td>{{ $new->id }}</td>
                                <td>{{ $new->title }}</td>
                                <td class="truncate-3-lines">{!! $new->outstanding ?? 'Chưa có mô tả.' !!}</td>
                                <td>
                                    @if($new->images)
                                        <img src="{{ asset('imgnew/' . $new->images) }}" alt="Image" style="width: 100px; height: auto;">
                                    @else
                                        <span class="text-muted">Không có ảnh</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-start">
                                        <a href="{{ route('new.show', ['id' => $new->id]) }}" class="btn btn-info btn-sm mr-2" title="Xem chi tiết" style="margin-right: 5px;">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('new.edit', ['id' => $new->id]) }}" class="btn btn-warning btn-sm mr-2" title="Chỉnh sửa" style="margin-right: 5px;">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('new.destroy', ['id' => $new->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">
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
