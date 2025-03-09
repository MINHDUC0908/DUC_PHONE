@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4 px-4">
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb small mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('user.index') }}" class="text-decoration-none">Quản lý nhân sự</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
                </ol>
            </nav>
            <h4 class="fw-semibold text-primary my-2">
                <i class="bi bi-person-gear me-1"></i>Cập Nhật Thông Tin Nhân Viên
            </h4>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12 col-md-9 mx-auto">
            <div class="card shadow-sm border-0 rounded-3 overflow-hidden">
                <div class="card-header d-flex align-items-center py-2 px-3" style="background: linear-gradient(to right, #f8f9fa, #e9ecef);">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-2 bg-primary text-white">
                            <span class="small fw-medium">{{ substr($user->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">{{ $user->name }}</h6>
                            <span class="text-muted x-small">{{ $user->email }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-3">
                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show py-2 d-flex align-items-center" role="alert">
                            <i class="bi bi-check-circle me-1 small"></i>
                            <div class="small">{{ session('status') }}</div>
                            <button type="button" class="btn-close small" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('user.update', $user->id) }}" method="POST" class="row g-2 mt-1">
                        @csrf
                        @method('PUT')

                        <div class="col-md-6 mb-2">
                            <label for="name" class="form-label small text-secondary mb-1">
                                <i class="bi bi-person-fill text-primary me-1"></i>Tên nhân viên
                            </label>
                            <input type="text" name="name" id="name" 
                                class="form-control form-control-sm @error('name') is-invalid @enderror" 
                                value="{{ old('name', $user->name) }}">
                            @error('name')
                                <div class="invalid-feedback x-small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="email" class="form-label small text-secondary mb-1">
                                <i class="bi bi-envelope-fill text-primary me-1"></i>Email công việc
                            </label>
                            <input type="email" name="email" id="email" 
                                class="form-control form-control-sm bg-light @error('email') is-invalid @enderror" 
                                value="{{ old('email', $user->email) }}">
                                {{-- readonly --}}
                            <div class="form-text x-small"><i class="bi bi-info-circle me-1"></i>Không thể thay đổi</div>
                            @error('email')
                                <div class="invalid-feedback x-small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mt-1">
                            <div class="d-flex align-items-center mb-2">
                                <div class="line-divider flex-grow-1"></div>
                                <span class="badge bg-light text-secondary mx-2">
                                    <i class="bi bi-shield-lock me-1"></i>Thay đổi mật khẩu
                                </span>
                                <div class="line-divider flex-grow-1"></div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="password" class="form-label small text-secondary mb-1">
                                Mật khẩu mới <span class="text-muted x-small">(tuỳ chọn)</span>
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="password" name="password" id="password" 
                                    class="form-control @error('password') is-invalid @enderror" 
                                    placeholder="••••••••">
                                <button class="btn btn-outline-secondary password-toggle" 
                                    type="button" onclick="togglePassword('password')">
                                    <i class="bi bi-eye small"></i>
                                </button>
                                @error('password')
                                    <div class="invalid-feedback x-small">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="progress mt-1" style="height: 3px;">
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 0%" 
                                    id="password-strength"></div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-2">
                            <label for="password_confirmation" class="form-label small text-secondary mb-1">
                                Xác nhận mật khẩu
                            </label>
                            <div class="input-group input-group-sm">
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                    class="form-control @error('password_confirmation') is-invalid @enderror" 
                                    placeholder="••••••••">
                                <button class="btn btn-outline-secondary password-toggle" 
                                    type="button" onclick="togglePassword('password_confirmation')">
                                    <i class="bi bi-eye small"></i>
                                </button>
                                @error('password_confirmation')
                                    <div class="invalid-feedback x-small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="d-flex gap-2 justify-content-between align-items-center">
                                <a href="{{ route('user.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Quay lại
                                </a>
                                <button type="submit" class="btn btn-sm btn-primary px-3">
                                    <i class="bi bi-save me-1"></i>Lưu thay đổi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer bg-light p-2">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock-history text-secondary me-1 small"></i>
                        <span class="x-small text-muted">Cập nhật lần cuối: {{ $user->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .x-small {
        font-size: 0.75rem;
    }
    
    .form-control-sm {
        height: 32px;
        font-size: 0.875rem;
    }
    
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.15rem rgba(13, 110, 253, 0.15);
    }
    
    .password-toggle {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .password-toggle:focus {
        box-shadow: none;
    }
    
    .line-divider {
        height: 1px;
        background-color: #dee2e6;
    }
    
    .btn-sm {
        font-size: 0.8125rem;
        padding: 0.325rem 0.65rem;
    }
</style>

<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const button = field.nextElementSibling;
        const icon = button.querySelector('i');
        
        if (field.type === "password") {
            field.type = "text";
            icon.classList.replace("bi-eye", "bi-eye-slash");
        } else {
            field.type = "password";
            icon.classList.replace("bi-eye-slash", "bi-eye");
        }
    }
    
    // Chỉ báo độ mạnh mật khẩu
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strength = document.getElementById('password-strength');
        let width = 0;
        let bgClass = 'bg-danger';
        
        if (password.length > 0) {
            if (password.length < 6) {
                width = 25;
                bgClass = 'bg-danger';
            } else if (password.length < 10) {
                width = 50;
                bgClass = 'bg-warning';
            } else if (password.length < 12) {
                width = 75;
                bgClass = 'bg-info';
            } else {
                width = 100;
                bgClass = 'bg-success';
            }
        }
        
        strength.style.width = width + '%';
        strength.className = 'progress-bar ' + bgClass;
    });
</script>
@endsection