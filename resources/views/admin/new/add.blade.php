@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2>Thêm sản phẩm mới</h2>
    <form action="{{ route('new.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="title">Tiêu đề bài viết</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}">
            
            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="outstanding">Mô tả bài viết</label>
            <textarea class="form-control @error('outstanding') is-invalid @enderror" id="outstanding" name="outstanding" rows="3">{{ old('outstanding') }}</textarea>
            @error('outstanding')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="form-group">
            <label for="images">Ảnh sản phẩm</label>
            <input type="file" class="form-control @error('images') is-invalid @enderror" id="images" name="images">
            <div id="image-preview"></div>
            @error('images')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary" style="margin: 20px 0;">Thêm bài viết</button>
    </form>
</div>
@endsection