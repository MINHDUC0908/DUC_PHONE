@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Thêm Mới Color</h2>
    <form action="{{ route('colors.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="color" class="form-label">Color Name</label>
            <input type="text" id="color" name="color" class="form-control" placeholder="Enter color name" value="{{ old('color') }}">
            <small class="form-text text-muted">E.g., yellow, blue, red</small>
            @error('color')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="product_id" class="form-label">Select Product</label>
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
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control" placeholder="Enter quantity" value="{{ old('quantity') }}" min="0">
            @error('quantity')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-success">Add Color</button>
        <a href="{{ route('colors.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
