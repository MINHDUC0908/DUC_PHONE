@extends('admin.layouts.app')
@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-header text-white">
            <h5 class="mb-0 text-primary">
                <i class="fas fa-plus-circle me-2"></i>Thêm sản phẩm mới
            </h5>
            <p class="text-muted mb-0">Điền thông tin sản phẩm cần thêm</p>
        </div>
        <div class="card-body">
            <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <!-- Tên sản phẩm -->
                    <div class="col-md-6 mb-3">
                        <label for="product_name" class="form-label">Tên sản phẩm</label>
                        <input type="text" class="form-control @error('product_name') is-invalid @enderror" id="product_name" name="product_name" value="{{ old('product_name') }}">
                        @error('product_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Giá sản phẩm -->
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Giá sản phẩm</label>
                        <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" step="0.01">
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Mô tả -->
                <div class="mb-3">
                    <label for="description" class="form-label">Mô tả sản phẩm</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tính năng nổi bật -->
                <div class="mb-3">
                    <label for="outstanding" class="form-label">Tính năng nổi bật</label>
                    <textarea class="form-control @error('outstanding') is-invalid @enderror" id="outstanding" name="outstanding" rows="3">{{ old('outstanding') }}</textarea>
                    @error('outstanding')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row">
                    <!-- Chọn danh mục -->
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label">Danh mục</label>
                        <select name="category_id" id="category_id" class="form-control" onchange="fetchBrands()">
                            @foreach ($categories as $item)
                                <option value="{{ $item->id }}">{{ $item->category_name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Chọn thương hiệu -->
                    <div class="col-md-6 mb-3">
                        <label for="brand_id">Brand</label>
                        <select name="brand_id" id="brand_id" class="form-control">
                            <!-- Thương hiệu sẽ được thêm qua AJAX -->
                        </select>
                    </div>
                </div>
                <!-- Ảnh sản phẩm -->
                <div class="mb-3">
                    <label for="images" class="form-label">Ảnh sản phẩm</label>
                    <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images" onchange="previewImages()">
                    <div class="d-flex flex-wrap mt-2" id="image-preview"></div>
                    @error('images')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Ảnh mô tả -->
                <div class="mb-3">
                    <label for="description_image" class="form-label">Ảnh mô tả</label>
                    <input type="file" class="form-control @error('description_image') is-invalid @enderror" id="description_image" name="description_image[]" multiple onchange="previewDescriptionImage()">
                    <div class="d-flex flex-wrap mt-2" id="description-image-preview"></div>
                    @error('description_image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success w-100"><i class="fas fa-save me-2"></i>Thêm sản phẩm</button>
            </form>
        </div>
    </div>
</div>

<script>
    function previewImages() {
        var preview = document.getElementById('image-preview');
        preview.innerHTML = "";

        var files = document.getElementById('images').files;
        for (var i = 0; i < files.length; i++) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.style.height = '100px';
                img.style.margin = '5px';
                img.style.borderRadius = '8px';
                preview.appendChild(img);
            }
            reader.readAsDataURL(files[i]);
        }
    }

    function previewDescriptionImage() {
        var preview = document.getElementById('description-image-preview');
        preview.innerHTML = "";

        var files = document.getElementById('description_image').files;
        for (var i = 0; i < files.length; i++) {
            var reader = new FileReader();
            reader.onload = function (e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.style.height = '100px';
                img.style.margin = '5px';
                img.style.borderRadius = '8px';
                preview.appendChild(img);
            }
            reader.readAsDataURL(files[i]);
        }
    }

        document.addEventListener("DOMContentLoaded", function() {
            fetchBrands();
        });

        function fetchBrands() {
            var categoryId = document.getElementById('category_id').value;
            var brandSelect = document.getElementById('brand_id');
            brandSelect.innerHTML = ''; 
            if (categoryId) {
                fetch(`/product/brands/${categoryId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            data.forEach(brand => {
                                var option = document.createElement('option');
                                option.value = brand.id;
                                option.textContent = brand.brand_name;
                                brandSelect.appendChild(option);
                            });
                        } else {
                            var option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'Không có thương hiệu';
                            brandSelect.appendChild(option);
                        }
                    })
                    .catch(error => console.error('Error fetching brands:', error));
            }
        }
    </script>
@endsection
