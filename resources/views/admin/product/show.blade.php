@extends('admin.layouts.app')

@section('content')
    <style>
        /* Main Image */
        .product-image {
            width: 100%;
            height: auto;
            object-fit: cover;
            filter: brightness(1.1) contrast(1.2);
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        /* Thumbnails */
        .thumbnails {
            display: flex;
            justify-content: start;
            overflow-x: auto;
            gap: 10px;
            padding-top: 15px;
            padding-bottom: 15px;
            border-top: 1px solid #ddd;
        }

        .thumbnail-item {
            cursor: pointer;
            width: 80px;
            height: 80px;
            border-radius: 8px;
            border: 2px solid #ddd;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .thumbnail-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .thumbnail-item:hover img {
            transform: scale(1.1);
        }

        .thumbnail-item:hover {
            border-color: #007bff;
        }

        /* Image Navigation Buttons */
        .image-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .image-container .prev, .image-container .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.2);
            color: #fff;
            border: none;
            padding: 1px 9px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 20px;
            transition: background 0.3s ease;
        }

        .image-container .prev {
            left: -40px;
        }

        .image-container .next {
            right: -40px;
        }

        .image-container .prev:hover, .image-container .next:hover {
            background: rgba(0, 0, 0, 0.8);
        }

        /* Product Info Section */
        .card-body {
            padding: 2rem;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .card-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
        }

        .card-text strong {
            font-weight: bold;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 1rem;
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .btn-warning {
            background-color: #f8c146;
            border: none;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .product-image {
                max-width: 100%;
            }

            .thumbnail-item {
                width: 60px;
                height: 60px;
            }

            .card-body {
                padding: 1rem;
            }

            .card-title {
                font-size: 1.5rem;
            }
        }
    </style>

    <div class="container mt-4">
        <div class="row">
            <!-- Product Images -->
            <div class="col-md-6">
                <div class="image-container">
                    <button class="prev" onclick="prevImage()"><i class="fas fa-chevron-left"></i></button>
                    <button class="next" onclick="nextImage()"><i class="fas fa-chevron-right"></i></button>
                    <img id="mainImage" src="{{ asset('imgProduct/' . $product->images) }}" class="" alt="{{ $product->name }}">
                </div>
                <div class="thumbnails mt-3">
                    <div class="thumbnail-item">
                        <img src="{{ asset('imgProduct/' . $product->images) }}" alt="Thumbnail" class="product-imgs" onclick="changeImage(this, 0)">
                    </div>  
                    @if ($product->description_image)
                        @foreach (json_decode($product->description_image) as $index => $descImage)
                            <div class="thumbnail-item">
                                <img src="{{ asset('imgDescriptionProduct/' . $descImage) }}" alt="Thumbnail" class="product-imgs" onclick="changeImage(this, {{ $index + 1 }})">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">{{ $product->product_name }}</h3>
                        <p class="card-text"><strong>Giá:</strong> {{ number_format($product->price, 0, ',', '.') }}₫</p>
                        <p class="card-text"><strong>Danh mục:</strong> {{ $product->category->category_name }}</p>
                        <p class="card-text"><strong>Thương hiệu:</strong> {{ $product->brand->brand_name }}</p>
                        <p class="card-text"><strong>Mô tả:</strong> {!! $product->description ?? 'Chưa có mô tả.' !!}</p>

                        <div class="mt-4">
                            <a href="{{ route('product.edit', ['id' => $product->id]) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            <form action="{{ route('product.destroy', ['id' => $product->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        let currentIndex = 0;
        function changeImage(element, index) {
            var mainImage = document.getElementById('mainImage');
            mainImage.src = element.src;
            currentIndex = index;
        }
        function startSlideshow() {
            var thumbnails = document.querySelectorAll('.thumbnail-item img');
            if (thumbnails.length > 0) {
                setInterval(() => {
                    var mainImage = document.getElementById('mainImage');
                    currentIndex = (currentIndex + 1) % thumbnails.length;
                    mainImage.src = thumbnails[currentIndex].src;
                }, 1500);
            }
        }
        function prevImage() {
            var thumbnails = document.querySelectorAll('.thumbnail-item img');
            if (thumbnails.length > 0) {
                currentIndex = (currentIndex - 1 + thumbnails.length) % thumbnails.length;
                var mainImage = document.getElementById('mainImage');
                mainImage.src = thumbnails[currentIndex].src;
            }
        }
        function nextImage() {
            var thumbnails = document.querySelectorAll('.thumbnail-item img');
            if (thumbnails.length > 0) {
                currentIndex = (currentIndex + 1) % thumbnails.length;
                var mainImage = document.getElementById('mainImage');
                mainImage.src = thumbnails[currentIndex].src;
            }
        }
        startSlideshow();
    </script>
@endsection
