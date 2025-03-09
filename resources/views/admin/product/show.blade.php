@extends('admin.layouts.app')

@section('content')
    <style>
        /* Variables */
        :root {
            --primary-color: #4a6cf7;
            --secondary-color: #f8fafc;
            --accent-color: #ff6b6b;
            --text-color: #2d3748;
            --light-gray: #e2e8f0;
            --dark-gray: #4a5568;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        /* Main Container */
        .product-container {
            max-width: 1200px;
            margin: 2rem auto;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        /* Product Image Styling */
        .product-image-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
            background-color: var(--secondary-color);
        }

        .product-image {
            width: 100%;
            height: 400px;
            object-fit: contain;
            transition: var(--transition);
            display: block;
            margin: 0 auto;
        }

        /* Image Navigation */
        .image-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
            z-index: 10;
        }

        .nav-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: rgba(255, 255, 255, 0.8);
            color: var(--text-color);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            opacity: 0.7;
            transition: var(--transition);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .nav-btn:hover {
            opacity: 1;
            background-color: white;
            transform: scale(1.1);
        }

        /* Thumbnails */
        .thumbnails-container {
            padding: 10px 0;
            margin-bottom: 1.5rem;
        }

        .thumbnails {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding: 10px 5px;
            scrollbar-width: thin;
            scrollbar-color: var(--primary-color) var(--light-gray);
        }

        .thumbnails::-webkit-scrollbar {
            height: 6px;
        }

        .thumbnails::-webkit-scrollbar-thumb {
            background-color: var(--primary-color);
            border-radius: 20px;
        }

        .thumbnail-item {
            cursor: pointer;
            min-width: 90px;
            height: 90px;
            border-radius: 8px;
            border: 2px solid var(--light-gray);
            overflow: hidden;
            position: relative;
            transition: var(--transition);
        }

        .thumbnail-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .thumbnail-item:hover {
            border-color: var(--primary-color);
            transform: translateY(-3px);
        }

        .thumbnail-item.active {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px var(--primary-color);
        }

        /* Product Info */
        .product-info {
            background: linear-gradient(to bottom, #ffffff, var(--secondary-color));
            border-radius: 12px;
            padding: 2.5rem;
            height: 100%;
            box-shadow: var(--shadow);
        }

        .product-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-color);
            margin-bottom: 1.5rem;
            border-bottom: 2px solid var(--light-gray);
            padding-bottom: 0.8rem;
        }

        .product-detail {
            margin-bottom: 1rem;
            display: flex;
            align-items: baseline;
        }

        .detail-label {
            font-weight: 600;
            min-width: 140px;
            color: var(--dark-gray);
            font-size: 1.1rem;
        }

        .detail-value {
            font-size: 1.1rem;
            color: var(--text-color);
            flex: 1;
        }

        .product-price {
            font-size: 1.5rem;
            color: var(--accent-color);
            font-weight: 700;
        }

        .product-description {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--light-gray);
        }

        .description-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .description-content {
            line-height: 1.8;
            color: var(--dark-gray);
        }

        /* Action Buttons */
        .action-buttons {
            margin-top: 2.5rem;
            display: flex;
            gap: 15px;
        }

        .btn-action {
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-edit {
            background-color: #f59e0b;
            color: white;
        }

        .btn-edit:hover {
            background-color: #d97706;
            transform: translateY(-2px);
        }

        .btn-delete {
            background-color: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background-color: #dc2626;
            transform: translateY(-2px);
        }

        /* Responsive Adjustments */
        @media (max-width: 992px) {
            .product-container {
                margin: 1rem;
            }
            
            .product-image {
                height: 350px;
            }
            
            .product-info {
                padding: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .product-image {
                height: 300px;
            }
            
            .thumbnail-item {
                min-width: 70px;
                height: 70px;
            }
            
            .product-title {
                font-size: 1.7rem;
            }
            
            .detail-label {
                min-width: 110px;
            }
        }

        @media (max-width: 576px) {
            .product-image-wrapper {
                margin-bottom: 1rem;
            }
            
            .product-image {
                height: 250px;
            }
            
            .product-info {
                padding: 1.2rem;
            }
            
            .product-title {
                font-size: 1.5rem;
                margin-bottom: 1rem;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <div class="container">
        <div class="product-container">
            <div class="row p-4">
                <!-- Product Images -->
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="product-image-wrapper">
                        <img id="mainImage" src="{{ asset('imgProduct/' . $product->images) }}" class="product-image" alt="{{ $product->product_name }}">
                        <div class="image-nav">
                            <button class="nav-btn prev-btn" onclick="prevImage()">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="nav-btn next-btn" onclick="nextImage()">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="thumbnails-container">
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
                </div>
                
                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="product-info">
                        <h1 class="product-title">{{ $product->product_name }}</h1>
                        
                        <div class="product-detail">
                            <span class="detail-label">Giá:</span>
                            <span class="detail-value product-price">{{ number_format($product->price, 0, ',', '.') }}₫</span>
                        </div>
                        
                        <div class="product-detail">
                            <span class="detail-label">Danh mục:</span>
                            <span class="detail-value">{{ $product->category->category_name }}</span>
                        </div>
                        
                        <div class="product-detail">
                            <span class="detail-label">Thương hiệu:</span>
                            <span class="detail-value">{{ $product->brand->brand_name }}</span>
                        </div>
                        
                        <div class="product-description">
                            <h3 class="description-title">Mô tả sản phẩm</h3>
                            <div class="description-content">
                                {!! $product->description ?? 'Chưa có mô tả cho sản phẩm này.' !!}
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <a href="{{ route('product.edit', ['id' => $product->id]) }}" class="btn btn-action btn-edit">
                                <i class="fas fa-edit"></i> Chỉnh sửa
                            </a>
                            <form action="{{ route('product.destroy', ['id' => $product->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-action btn-delete">
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