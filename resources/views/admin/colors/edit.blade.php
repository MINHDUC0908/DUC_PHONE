@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Tiêu đề -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Edit Color</h2>
        <a href="{{ route('colors.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <!-- Form chỉnh sửa -->
    <form action="{{ route('colors.update', $color->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Color Name -->
        <div class="mb-3">
            <label for="color" class="form-label">Color Name</label>
            <input type="text" id="color" name="color" class="form-control" 
                   value="{{ old('color', $color->color) }}" placeholder="Enter color name">
            @error('color')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Select Product -->
        <div class="mb-3">
            <label for="product_id" class="form-label">Product</label>
            <select id="product_id" name="product_id" class="form-control">
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" {{ $color->product_id == $product->id ? 'selected' : '' }}>
                        {{ $product->product_name }}
                    </option>
                @endforeach
            </select>
            @error('product_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Quantity -->
        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control" 
                   value="{{ old('quantity', $color->quantity) }}" min="0" placeholder="Enter quantity">
            @error('quantity')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-success">
            <i class="fas fa-save"></i> Save Changes
        </button>
    </form>
</div>
@endsection
