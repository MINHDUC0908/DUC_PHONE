@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4 class="mb-0">Cấp quyền cho Nhân sự</h4>
                </div>
                <div class="card-body p-4">
                    @if (session('status'))
                        <div class="alert alert-success text-center">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <p class="text-center fw-bold text-uppercase">{{ $user->name }}</p>
                    
                    <form action="{{ route('storeRole', ['id' => $user->id]) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Chọn Vai Trò</label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($roles as $role)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="role" id="role{{ $role->id }}" value="{{ $role->name }}"
                                            @if($all_colum_roles && $role->id == $all_colum_roles->id) checked @endif>
                                        <label class="form-check-label" for="role{{ $role->id }}">
                                            {{ $role->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary fw-bold">Cập Nhật Quyền</button>
                        </div>
                    </form>
                    <hr>
                    <form action="{{ route('storeRoles') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="role" class="form-label fw-bold">Thêm Vai Trò Mới</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                                <input type="text" name="role" id="role" class="form-control" required value="{{ old('role') }}" placeholder="Nhập vai trò mới">
                            </div>
                            @error('role')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success fw-bold">Thêm Vai Trò</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
