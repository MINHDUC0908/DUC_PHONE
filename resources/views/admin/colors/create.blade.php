@extends('admin.layouts.app')

@section('content')
<style>
    #product_id {
        width: 100%;
        max-height: 250px;
        overflow-y: auto;
    }
    .color-preview {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid #ccc;
        display: inline-block;
        margin-left: 10px;
        z-index: 100;
    }
</style>

<div class="container-fluid mt-4">
    <div class="card shadow-lg p-4">
        <h2 class="mb-4"><i class="bi bi-palette me-2"></i>Thêm Mới Màu Sắc</h2>
        <form action="{{ route('colors.store') }}" method="POST">
            @csrf
            <div class="mb-3 d-flex align-items-center">
                <div class="flex-grow-1">
                    <label for="color" class="form-label fw-bold">
                        <i class="bi bi-palette2 me-2"></i>Tên Màu
                    </label>
                    <input type="text" id="color" name="color" class="form-control" 
                        placeholder="Nhập tên màu hoặc mã HEX..." value="{{ old('color') }}"
                        oninput="document.getElementById('colorPreview').style.backgroundColor = this.value">
                    <small class="form-text text-muted">VD: red, blue, yellow hoặc #ff0000</small>
                    @error('color')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div id="colorPreview" class="color-preview" style="background-color: {{ old('color') }}"></div>
            </div>

            <div class="mb-3">
                <label for="product_id" class="form-label fw-bold">
                    <i class="bi bi-box-seam me-2"></i>Chọn Sản Phẩm
                </label>
                <select id="product_id" name="product_id" class="form-select">
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->product_name }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="quantity" class="form-label fw-bold">
                    <i class="bi bi-sort-numeric-up me-2"></i>Số Lượng
                </label>
                <input type="number" id="quantity" name="quantity" class="form-control" 
                    placeholder="Nhập số lượng" value="{{ old('quantity') }}" min="0">
                @error('quantity')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('colors.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left-circle me-2"></i>Hủy
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-plus-circle me-2"></i>Thêm Màu
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo Select2
        $('#product_id').select2({
            placeholder: "Chọn sản phẩm...",
            allowClear: true
        });
        
        // Xử lý cập nhật màu sắc
        var colorInput = document.getElementById('color');
        var colorPreview = document.getElementById('colorPreview');
        
        // Hàm cập nhật màu
        function updateColor() {
            var colorValue = colorInput.value.trim();
            colorPreview.style.backgroundColor = colorValue;
        }
        
        // Đăng ký nhiều loại sự kiện để đảm bảo cập nhật trong mọi trường hợp
        colorInput.addEventListener('input', updateColor);
        colorInput.addEventListener('change', updateColor);
        colorInput.addEventListener('keyup', updateColor);
        colorInput.addEventListener('paste', function() {
            setTimeout(updateColor, 10);
        });
        
        // Cập nhật ban đầu
        updateColor();
    });
</script>
@endsection