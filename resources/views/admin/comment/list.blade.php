@extends('admin.layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/comment.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<div class="container-fluid mt-4 px-4">
    <div class="card shadow-lg rounded-4 border-0 animate__animated animate__fadeIn">
        <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center rounded-top-4 py-3">
            <h5 class="mb-0 d-flex align-items-center">
                <i class="bi bi-chat-square-text-fill me-2"></i>Quản lý bình luận
            </h5>
            <div>
                <button class="btn btn-sm btn-light rounded-pill shadow-sm">
                    <i class="bi bi-filter me-1"></i> Lọc
                </button>
                <button class="btn btn-sm btn-light rounded-pill shadow-sm ms-2">
                    <i class="bi bi-download me-1"></i> Xuất
                </button>
            </div>
        </div>
        <div class="card-body bg-light p-4">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle shadow-sm rounded-3 overflow-hidden">
                    <thead class="bg-gradient-primary text-white text-center">
                        <tr>
                            <th class="py-3" width="25%"><i class="bi bi-box2-fill me-1"></i> Sản phẩm</th>
                            <th class="py-3" width="15%"><i class="bi bi-people-fill me-1"></i> Người dùng</th>
                            <th class="py-3" width="30%"><i class="bi bi-chat-left-text-fill me-1"></i> Bình luận</th>
                            <th class="py-3" width="10%"><i class="bi bi-calendar-event me-1"></i> Ngày tạo</th>
                            <th class="py-3" width="15%"><i class="bi bi-gear-fill me-1"></i> Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($comments as $index => $comment)
                            <tr id="comment-{{$comment->id}}" class="fw-semibold align-middle text-center comment-row">
                                <td class="text-start" style="max-width: 300px;">
                                    <div class="d-flex align-items-center">
                                        <div class="product-icon me-2 rounded-circle bg-success-subtle p-2">
                                            <i class="bi bi-box-seam-fill text-success"></i>
                                        </div>
                                        <div class="text-truncate">
                                            <span class="fw-semibold">{{$comment->product->product_name}}</span>
                                            <div class="small text-muted text-truncate">
                                                <i class="bi bi-tag-fill me-1"></i> 
                                                ID: {{$comment->product->id}}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-start text-truncate" style="max-width: 150px;">
                                    <div class="d-flex align-items-center">
                                        <div class="d-flex align-items-center justify-content-center me-2">
                                            @if($comment->customer->image)
                                                <img src="{{ asset('storage/imgCustomer/' . $comment->customer->image) }}" 
                                                        alt="{{ $comment->customer->name }}" 
                                                        class="img-thumbnail rounded-circle"
                                                        style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-primary text-white d-flex align-items-center justify-content-center" 
                                                        style="width: 40px; height: 40px; border-radius: 50%; border: 5px solid white; box-shadow: 0 0 0 1px #dee2e6;">
                                                    <span class="small fw-medium">{{ substr($comment->customer->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        @php
                                            $nameParts = explode(' ', trim($comment->customer->name));
                                            $shortName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, -2)) : $nameParts[0];
                                        @endphp   
                                        <span class="small fw-medium">{{ $shortName }}</span>
                                    </div>
                                </td>
                                <td class="text-start text-wrap" style="max-width: 300px;">
                                    <div class="comment-content p-2 rounded-3 bg-white shadow-sm">
                                        {{ $comment->content }}
                                    </div>
                                </td>
                                <td class="text-muted">
                                    <div class="d-flex flex-column align-items-center">
                                        <span><i class="bi bi-calendar2-event me-1"></i> {{ $comment->created_at->format('d/m/Y') }}</span>
                                        <span class="small"><i class="bi bi-clock me-1"></i> {{ $comment->created_at->format('H:i') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill" data-bs-toggle="collapse" data-bs-target="#replyForm{{ $comment->id }}" title="Trả lời">
                                            <i class="bi bi-reply-fill me-1"></i> Trả lời
                                        </button>    
                                        <button type="button" 
                                                class="btn btn-light btn-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                title="Xóa thuộc tính"
                                                data-bs-placement="top">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>                                
                                    </div>
                                </td>
                            </tr>
                            <!-- Hiển thị danh sách trả lời -->
                            @foreach ($comment->replies as $reply)
                                <tr id="comment-{{$reply->id}}" class="table-light reply-row" data-parent="{{ $comment->id }}">
                                    <td></td>
                                    <td colspan="4" class="text-start ps-5">
                                        <div class="reply-content p-3 rounded-3 bg-white shadow-sm position-relative ms-3">
                                            <div class="reply-badge bg-success text-white px-2 py-1 rounded-pill position-absolute top-0 start-0 translate-middle">
                                                <i class="bi bi-person-badge-fill me-1"></i> Admin
                                            </div>
                                            <div class="d-flex justify-content-between mb-2 mt-1 pt-1">
                                                <span class="fw-semibold text-success">{{ $reply->admin->name ?? 'Ẩn danh' }}</span>
                                                <span class="text-muted small"><i class="bi bi-clock-history me-1"></i> {{ $reply->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="reply-text">{{ $reply->content }}</div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill btn-sm">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                            <!-- Form trả lời -->
                            <tr id="replyForm{{ $comment->id }}" class="collapse bg-white">
                                <td colspan="6" class="p-2">
                                    <form action="{{ route('comment.reply', $comment->id) }}" method="POST" class="reply-form bg-white rounded-3 shadow-sm p-2 border-start border-4 border-primary ms-4">
                                        @csrf
                                        <div class="d-flex align-items-center mb-1">
                                            <span class="badge bg-primary rounded-pill me-2 px-2 py-1">
                                                <i class="bi bi-reply-fill me-1"></i> Trả lời
                                            </span>
                                            <small class="text-muted">{{ $comment->customer->name ?? 'Ẩn danh' }}</small>
                                        </div>
                                        <input type="hidden" name="product_id" value="{{ $comment->product_id }}">
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="content" class="form-control form-control-sm border-light shadow-sm" 
                                                   placeholder="Nhập nội dung phản hồi..." required>
                                            <button type="submit" class="btn btn-sm btn-primary px-2">
                                                <i class="bi bi-send"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-light px-2" 
                                                   data-bs-toggle="collapse" data-bs-target="#replyForm{{ $comment->id }}">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </div>
                                        <div class="form-text small mt-1">
                                            <i class="bi bi-info-circle-fill me-1 text-primary"></i>
                                            Phản hồi sẽ được hiển thị dưới tên admin của bạn.
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@foreach ($comments as $comment)
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-danger text-white rounded-top-4">
                    <h5 class="modal-title d-flex align-items-center text-sm">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Xác nhận xóa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted text-sm">Bạn có chắc chắn muốn xóa bình luận này?</p>
                    <div class="bg-light p-3 rounded-3 border d-flex align-items-start">
                        <i class="bi bi-quote text-danger me-2"></i>
                        <p class="mb-0 text-muted text-sm">{{ $comment->content }}</p>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 text-sm" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Hủy
                    </button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 text-sm delete-btn" 
                            data-id="{{ $comment->id }}" 
                            data-bs-dismiss="modal">
                        <i class="bi bi-trash me-1"></i> Xóa
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', async () => {
                const commentId = button.getAttribute('data-id');
                const url = `/comment/${commentId}`;

                try {
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        const commentRow = document.getElementById(`comment-${commentId}`);
                        if (commentRow) {
                            commentRow.remove(); // Xóa bình luận chính
                        }

                        // Xóa luôn form trả lời (nếu có)
                        const replyForm = document.getElementById(`replyForm${commentId}`);
                        if (replyForm) {
                            replyForm.remove();
                        }
                        document.querySelectorAll(`.reply-row[data-parent="${commentId}"]`).forEach(reply => reply.remove());
                    } else {
                        alert("Xóa không thành công. Vui lòng thử lại!");
                    }
                } catch (error) {
                    console.error('Lỗi:', error);
                }
            });
        });
    });
</script>

@endsection