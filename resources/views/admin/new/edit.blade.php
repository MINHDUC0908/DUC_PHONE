@extends('admin.layouts.app')

@section('styles')
<style>
    .premium-card {
        border-radius: 12px;
        border: none;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .premium-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }
    
    .premium-header {
        background: linear-gradient(135deg, #4a6bff 0%, #2541b8 100%);
        padding: 25px;
        position: relative;
        overflow: hidden;
    }
    
    .premium-header::before {
        content: "";
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: rgba(255, 255, 255, 0.1);
        transform: rotate(25deg);
    }
    
    .form-control, .input-group-text {
        border-radius: 8px;
        padding: 12px 15px;
        transition: all 0.2s;
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.25);
        border-color: #4a6bff;
    }
    
    .input-group .form-control {
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    
    .input-group-text {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
        background: #f0f4ff;
        color: #4a6bff;
        border: 1px solid #ced4da;
        border-right: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #4a6bff 0%, #2541b8 100%);
        border: none;
        box-shadow: 0 4px 10px rgba(37, 65, 184, 0.3);
        border-radius: 8px;
        padding: 12px 25px;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(37, 65, 184, 0.4);
    }
    
    .btn-outline-secondary {
        border-radius: 8px;
        padding: 12px 25px;
        border: 2px solid #dee2e6;
        color: #495057;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-outline-secondary:hover {
        background-color: #f8f9fa;
        color: #212529;
        transform: translateY(-2px);
    }
    
    .image-upload-area {
        border: 2px dashed #c5cfe0;
        border-radius: 12px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s;
        background: #f8faff;
    }
    
    .image-upload-area:hover, .image-upload-area.dragover {
        border-color: #4a6bff;
        background: #f0f4ff;
    }
    
    .preview-container {
        position: relative;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
    }
    
    .preview-container:hover {
        transform: scale(1.02);
        box-shadow: 0 15px 25px rgba(0, 0, 0, 0.15);
    }
    
    .form-label {
        font-size: 1rem;
        margin-bottom: 0.5rem;
        color: #37474f;
    }
    
    .invalid-feedback {
        display: flex;
        align-items: center;
        margin-top: 6px;
        font-size: 0.85rem;
    }
    
    .section-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #e0e0e0, transparent);
        margin: 30px 0;
    }

    .current-image {
        opacity: 0.7;
        transition: all 0.3s;
    }

    .current-image:hover {
        opacity: 1;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="">
            <div class="premium-card shadow-lg">
                <div class="premium-header">
                    <div class="d-flex align-items-center">
                        <div class="position-relative me-4">
                            <i class="fas fa-edit fa-3x text-white opacity-75"></i>
                            <i class="fas fa-pen position-absolute" style="font-size: 14px; top: 50%; left: 50%; transform: translate(-50%, -50%);"></i>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold text-white">Chỉnh Sửa Bài Viết</h3>
                            <p class="mb-0 text-white opacity-75">Cập nhật thông tin bài viết của bạn</p>
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4 p-lg-3">
                    <form action="{{ route('new.update', $new->id) }}" method="POST" enctype="multipart/form-data" id="productForm">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-4 pb-2">
                            <label for="title" class="form-label fw-bold">
                                <i class="fas fa-heading me-2 text-primary"></i>Tiêu đề bài viết
                            </label>
                            <div class="input-group input-group-lg shadow-sm">
                                <span class="input-group-text">
                                    <i class="fas fa-edit"></i>
                                </span>
                                <input type="text" 
                                      class="form-control @error('title') is-invalid @enderror" 
                                      id="title" 
                                      name="title" 
                                      value="{{ old('title', $new->title) }}" 
                                      placeholder="Nhập tiêu đề hấp dẫn cho bài viết của bạn">
                            </div>
                            @error('title')
                                <div class="invalid-feedback ps-1">
                                    <i class="fas fa-exclamation-circle me-1 text-danger"></i> {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted mt-2 d-block">Tiêu đề nên ngắn gọn và thu hút người đọc</small>
                        </div>
                        
                        <div class="section-divider"></div>
                        
                        <div class="mb-4 pb-2">
                            <label for="outstanding" class="form-label fw-bold">
                                <i class="fas fa-align-left me-2 text-primary"></i>Mô tả bài viết
                            </label>
                            <div class="shadow-sm">
                                <span class="input-group-text">
                                    <i class="fas fa-paragraph"></i>
                                </span>
                                <textarea class="form-control @error('outstanding') is-invalid @enderror" 
                                         id="outstanding" 
                                         name="outstanding" 
                                         rows="5" 
                                         placeholder="Mô tả ngắn gọn về nội dung chính của bài viết...">{{ old('outstanding', $new->outstanding) }}</textarea>
                            </div>
                            @error('outstanding')
                                <div class="invalid-feedback ps-1">
                                    <i class="fas fa-exclamation-circle me-1 text-danger"></i> {{ $message }}
                                </div>
                            @enderror
                            <small class="text-muted mt-2 d-block">Mô tả sẽ hiển thị ở trang chủ và kết quả tìm kiếm</small>
                        </div>
                        
                        <div class="section-divider"></div>
                        
                        <div class="mb-4 pb-2">
                            <label for="images" class="form-label fw-bold">
                                <i class="fas fa-image me-2 text-primary"></i>Ảnh sản phẩm
                            </label>

                            <!-- Current Image -->
                            <div class="current-image mb-4">
                                <div class="preview-container">
                                    <div class="position-relative">
                                        <img src="{{ asset('imgNew/' . $new->images) }}" 
                                             class="img-fluid rounded-3 w-100" 
                                             style="max-height: 350px; object-fit: cover;"
                                             alt="Current image">
                                        <div class="position-absolute top-0 end-0 p-3">
                                            <span class="badge bg-info">Ảnh hiện tại</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="image-upload-area" id="uploadArea">
                                <div class="py-3 d-flex flex-column align-items-center">
                                    <div class="bg-white p-3 rounded-circle shadow-sm mb-3">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-primary"></i>
                                    </div>
                                    <h5 class="mb-2">Tải lên ảnh mới</h5>
                                    <p class="text-muted mb-3">Hỗ trợ định dạng: JPG, PNG, GIF (Tối đa 5MB)</p>
                                    
                                    <div class="position-relative">
                                        <button type="button" class="btn btn-primary px-4" id="uploadButton">
                                            <i class="fas fa-images me-2"></i>Chọn ảnh mới
                                        </button>
                                        <input type="file" 
                                              class="position-absolute top-0 start-0 opacity-0 w-100 h-100" 
                                              id="images" 
                                              name="images" 
                                              accept="image/*" 
                                              onchange="previewImage(event)"
                                              style="cursor: pointer;">
                                    </div>
                                </div>
                            </div>
                            
                            @error('images')
                                <div class="invalid-feedback d-block mt-2 ps-1">
                                    <i class="fas fa-exclamation-circle me-1 text-danger"></i> {{ $message }}
                                </div>
                            @enderror
                            
                            <div id="image-preview" class="mt-4"></div>
                        </div>
                        
                        <div class="section-divider"></div>
                        
                        <div class="d-flex justify-content-between align-items-center pt-2">
                            <a href="{{ route('new.list') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                            </a>
                            <button type="submit" class="btn btn-primary px-5">
                                <i class="fas fa-save me-2"></i>Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts for image preview --}}
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (!file) return;
        
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('image-preview');
            const uploadArea = document.getElementById('uploadArea');
            
            // Ẩn khu vực upload khi đã chọn ảnh
            uploadArea.style.display = 'none';
            
            output.innerHTML = `
                <div class="preview-container">
                    <div class="position-relative">
                        <img src="${reader.result}" class="img-fluid rounded-3 w-100" style="max-height: 350px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 p-3">
                            <button type="button" class="btn btn-light btn-sm rounded-circle shadow" onclick="removeImage()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="bg-light p-3 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1 text-truncate" style="max-width: 250px;">Ảnh mới: ${file.name}</h6>
                                <span class="text-muted small">${(file.size / 1024).toFixed(1)} KB</span>
                            </div>
                            <span class="badge bg-primary">Ảnh mới</span>
                        </div>
                    </div>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
    
    function removeImage() {
        document.getElementById('images').value = '';
        document.getElementById('image-preview').innerHTML = '';
        document.getElementById('uploadArea').style.display = 'block';
    }
    
    // Drag & Drop functionality
    document.addEventListener('DOMContentLoaded', function() {
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('images');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });
        
        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
        
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });
        
        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });
        
        function highlight() {
            uploadArea.classList.add('dragover');
        }
        
        function unhighlight() {
            uploadArea.classList.remove('dragover');
        }
        
        uploadArea.addEventListener('drop', handleDrop, false);
        
        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length) {
                fileInput.files = files;
                const event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);
            }
        }
    });
</script>
@endsection