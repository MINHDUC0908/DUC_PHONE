@extends('admin.layouts.app')

@section('content')
<style>
    .table th {
        font-weight: 600;
        color: #4a5568;
    }
    
    .table td {
        border-bottom: 1px solid #f0f0f0;
    }
    
    .badge {
        font-weight: 500;
        padding: 0.5em 1em;
    }
    
    .btn-sm {
        padding: 0.4rem;
        line-height: 1;
        border-radius: 0.375rem;
    }
    
    .btn-light {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
    }
    
    .btn-light:hover {
        background: #e9ecef;
        border-color: #dee2e6;
    }
    
    .alert {
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .card {
        overflow: hidden;
    }

</style>
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-3 shadow-sm mb-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">
                <i class="fas fa-newspaper me-2"></i>Quản lý bài viết
            </h2>
            <p class="text-muted mb-0">Quản lý và cập nhật thông tin bài viết</p>
        </div>
        <div>
            <a href="{{ route('new.create') }}" class="btn btn-primary btn-md">
                <i class="fas fa-plus-circle me-2"></i>Thêm bài viết mới
            </a>
        </div>
    </div>
    
    {{-- Thông báo --}}
    @if(session('status'))
        <div class="alert custom-alert alert-success alert-dismissible fade show border-0 shadow" role="alert" id="status-alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon-container me-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h5 class="alert-heading mb-1">Thành công!</h5>
                    <p class="mb-0">{{ session('status') }}</p>
                </div>
            </div>
            <div class="progress mt-2" style="height: 3px;">
                <div id="alert-progress-bar" class="progress-bar bg-white" style="width: 100%;"></div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow border-0 rounded-3">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-list me-2"></i>Danh sách bài viết
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="newsTable">
                    <thead class="bg-light">
                        <tr>
                            <th scope="col" class="py-3 text-center" width="5%">ID</th>
                            <th scope="col" class="py-3" width="30%">Tên bài viết</th>
                            <th scope="col" class="py-3" width="40%">Mô tả</th>
                            <th scope="col" class="py-3" width="10%">Hình ảnh</th>
                            <th scope="col" class="py-3 text-center" width="15%">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($news as $new)
                            <tr>
                                <td class="text-center align-middle">{{ $new->id }}</td>
                                <td class="align-middle fw-bold text-dark">{{ $new->title }}</td>
                                <td class="align-middle text-muted truncate-3-lines">{{ Str::limit(html_entity_decode(strip_tags($new->outstanding)), 500, '...') }}
                                </td>
                                <td class="align-middle">
                                    @if($new->images)
                                        <img src="{{ asset('imgnew/' . $new->images) }}" alt="Image" class="rounded shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                                    @else
                                        <span class="badge bg-light text-secondary">
                                            <i class="fas fa-image-slash me-1"></i>Không có ảnh
                                        </span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('new.show', ['id' => $new->id]) }}" class="btn btn-light btn-sm" data-bs-toggle="tooltip" title="Xem chi tiết">
                                            <i class="fas fa-eye text-info"></i>
                                        </a>
                                        <a href="{{ route('new.edit', ['id' => $new->id]) }}" class="btn btn-light btn-sm" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                            <i class="fas fa-edit text-warning"></i>
                                        </a>
                                        <form action="{{ route('new.destroy', ['id' => $new->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này không?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-light btn-sm" data-bs-toggle="tooltip" title="Xóa bài viết">
                                                <i class="fas fa-trash text-danger"></i>
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
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
            <small>
                Hiển thị <strong>{{ $news->firstItem() }}</strong> - <strong>{{ $news->lastItem() }}</strong> trong tổng số <strong>{{ $news->total() }}</strong> bài viết
            </small>            
        </div>
        <div>
            {{ $news->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
        </div>        
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endsection
