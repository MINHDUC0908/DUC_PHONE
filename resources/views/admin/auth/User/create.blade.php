@extends('admin.layouts.app')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="row">
        <!-- Tiêu đề trang -->
        <div class="col-12 mb-4">
            <h3 class="fw-bold text-primary border-bottom pb-3">
                <i class="bi bi-person-plus-fill me-2"></i>Quản Lý Nhân Sự
            </h3>
        </div>
        
        <!-- Card form bên trái -->
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-lg mb-4">
                <div class="card-header bg-primary text-white py-2">
                    <h5 class="mb-0">
                        <i class="bi bi-person-plus me-2"></i>Thêm Nhân sự Mới
                    </h5>
                </div>
                <div class="card-body p-3">
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
                            <i class="bi bi-check-circle me-1"></i>{{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('user.store') }}" method="POST" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label for="name" class="form-label">
                                <i class="bi bi-person me-1 text-primary"></i>Tên nhân viên
                            </label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                value="{{ old('name') }}" placeholder="Nhập tên đầy đủ">
                            @error('name')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">
                                <i class="bi bi-envelope me-1 text-primary"></i>Email công việc
                            </label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                                value="{{ old('email') }}" placeholder="example@duccomputer.com">
                            @error('email')
                                <div class="invalid-feedback small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="form-label">
                                <i class="bi bi-shield-lock me-1 text-primary"></i>Mật khẩu
                            </label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    placeholder="Nhập mật khẩu an toàn">
                                <button class="btn btn-outline-secondary toggle-password" 
                                    type="button" onclick="togglePassword('password')">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="password_confirmation" class="form-label">
                                <i class="bi bi-shield-check me-1 text-primary"></i>Xác nhận mật khẩu
                            </label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                    class="form-control @error('password_confirmation') is-invalid @enderror" 
                                    placeholder="Nhập lại mật khẩu">
                                <button class="btn btn-outline-secondary toggle-password" 
                                    type="button" onclick="togglePassword('password_confirmation')">
                                    <i class="bi bi-eye-fill"></i>
                                </button>
                                @error('password_confirmation')
                                    <div class="invalid-feedback small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('user.index') }}" class="btn btn-light">
                                    <i class="bi bi-arrow-left me-1"></i>Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-person-plus-fill me-1"></i>Thêm Nhân Sự
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Thông tin hướng dẫn bên phải -->
        <div class="col-lg-4">
            <div class="card shadow border-0 rounded-lg bg-light mb-4">
                <div class="card-header bg-info text-white py-2">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>Hướng dẫn
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="text-primary"><i class="bi bi-person-badge me-1"></i>Thông tin nhân sự</h6>
                        <p class="small">Nhập đầy đủ thông tin nhân viên mới. Đảm bảo email không trùng lặp trong hệ thống.</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-primary"><i class="bi bi-shield-lock me-1"></i>Yêu cầu mật khẩu</h6>
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item bg-transparent py-1">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>Ít nhất 8 ký tự
                            </li>
                            <li class="list-group-item bg-transparent py-1">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>Bao gồm chữ hoa và chữ thường
                            </li>
                            <li class="list-group-item bg-transparent py-1">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>Ít nhất 1 ký tự đặc biệt
                            </li>
                        </ul>
                    </div>
                    
                    <div class="alert alert-warning py-2 small">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                        <strong>Lưu ý:</strong> Hãy thông báo cho nhân viên mới đổi mật khẩu khi đăng nhập lần đầu.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword(fieldId) {
        let field = document.getElementById(fieldId);
        let button = field.nextElementSibling;
        let icon = button.querySelector('i');
        
        if (field.type === "password") {
            field.type = "text";
            icon.classList.replace("bi-eye-fill", "bi-eye-slash-fill");
        } else {
            field.type = "password";
            icon.classList.replace("bi-eye-slash-fill", "bi-eye-fill");
        }
    }
</script>

<style>
    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.25);
    }
    
    .btn-primary {
        background-image: linear-gradient(to right, #0d6efd, #0b5ed7);
        border: none;
        transition: all 0.3s;
    }
    
    .btn-primary:hover {
        background-image: linear-gradient(to right, #0b5ed7, #084298);
        transform: translateY(-2px);
    }
    
    .card {
        transition: all 0.3s;
    }
    
    .card:hover {
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    
    /* Giảm kích thước chữ cho form bên trái */
    .col-lg-8 .form-label {
        font-size: 0.9rem;
    }
    
    .col-lg-8 .btn {
        font-size: 0.9rem;
    }
    
    .col-lg-8 .card-header h5 {
        font-size: 1.1rem;
    }
    
    .col-lg-8 input::placeholder {
        font-size: 0.85rem;
    }
</style>
@endsection