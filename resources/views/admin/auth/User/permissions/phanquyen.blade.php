@extends('admin.layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Cấp quyền cho user -->
    <div class="card shadow-lg border-0 rounded-lg">
        <div class="card-header bg-gradient-info text-dark text-center py-3">
            <h4 class="mb-0 fw-bold">🔑 Cấp Quyền Cho User</h4>
        </div>
        <div class="card-body p-4 bg-light">
            <h5 class="fw-bold text-center text-dark">👤 User: <span class="text-primary">{{ $user->name }}</span></h5>
            <p class="text-center text-dark">🛡 Vai trò hiện tại: <span class="badge bg-success px-3 py-2">{{ $name_role }}</span></p>
            
            <form action="{{ route('storePermission', ['id' => $user->id]) }}" method="POST" class="mt-3">
                @csrf
                <div class="row d-flex flex-wrap">
                    @foreach ($permission as $item)
                        <div class="form-check col-md-4 mb-2">
                            <input type="checkbox" name="permissions[]" value="{{ $item->name }}" id="perm-{{ $item->id }}" class="form-check-input"
                                @foreach ($get_permission_via_role as $get)
                                    @if ($get->id === $item->id)
                                        checked
                                    @endif
                                @endforeach>
                            <label for="perm-{{ $item->id }}" class="form-check-label text-dark">
                                <i class="bi bi-check-circle text-success"></i> {{ $item->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm">🚀 Cập Nhật Quyền</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Thêm quyền mới -->
    <div class="card shadow-lg border-0 rounded-lg mt-4">
        <div class="card-header bg-gradient-info text-dark text-center py-3">
            <h4 class="mb-0 fw-bold">➕ Thêm Quyền Mới</h4>
        </div>
        <div class="card-body p-4 bg-light">
            <form action="{{ route('Permission') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="permission" class="form-label fw-bold text-dark">🏷️ Tên Quyền</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary text-light"><i class="bi bi-key-fill"></i></span>
                        <input type="text" name="permission" id="permission" class="form-control border-secondary shadow-sm"
                            required value="{{ old('permission') }}" placeholder="Nhập tên quyền...">
                    </div>
                    @error('permission')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success fw-bold shadow-sm">✔️ Thêm Quyền</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
