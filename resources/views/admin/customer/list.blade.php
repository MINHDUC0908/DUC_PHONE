@extends('admin.layouts.app')

@section('content')
<style>
    .table th {
        font-weight: 600;
        color: #4a5568;
    }
    .table td {
        border-bottom: 1px solid #f0f0f0;
    }
    .btn-sm {
        padding: 0.4rem 0.75rem;
        line-height: 1.2;
        border-radius: 0.375rem;
    }
    .btn-success, .btn-danger {
        width: 140px;
    }
</style>
    <div class="container-fluid px-4 py-4">
        <div class="d-flex justify-content-between align-items-center bg-white p-4 rounded-3 shadow-sm mb-4">
            <div>
                <h2 class="fw-bold text-primary mb-1">
                    <i class="fas fa-users me-2"></i> Quản lý người dùng
                </h2>
                <p class="text-muted mb-0">Danh sách và trạng thái người dùng</p>
            </div>
        </div>

    {{-- Thông báo --}}
    @if(session('status'))
        <div class="alert custom-alert alert-success alert-dismissible fade show border-0 shadow" role="alert" id="status-alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon-container me-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h5 class="alert-heading mb-1">Thành công!</h5>
                    <p class="mb-0">{{ session('status') }}</p>
                </div>
            </div>
            <div class="progress mt-2" style="height: 3px;">
                <div id="alert-progress-bar" class="progress-bar bg-white" style="width: 100%;"></div>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

        <div class="card shadow border-0 rounded-3">
            <div class="card border-0 rounded-3">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-list me-2"></i>Danh sách người dùng
                    </h5>
                    <!-- Ô tìm kiếm -->
                    <div class="search-container">
                        <input type="text" id="searchCustomer" class="form-control px-4" 
                            placeholder="🔍 Tìm kiếm người dùng..." onkeyup="filterTable()">
                    </div>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="searchCustomer">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 text-center">ID</th>
                                <th scope="col" class="py-3">Hình ảnh</th>
                                <th class="py-3">Tên người dùng</th>
                                <th class="py-3">Email</th>
                                <th class="py-3 text-center">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customers as $customer)
                                <tr>
                                    <td class="text-center">{{ $customer->id }}</td>
                                    <td class="align-middle text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            @if($customer->image)
                                                <img src="{{ asset('storage/imgCustomer/' . $customer->image) }}" 
                                                     alt="{{ $customer->name }}" 
                                                     class="img-thumbnail rounded-circle"
                                                     style="width: 45px; height: 45px; object-fit: cover;">
                                            @else
                                                <div class="bg-primary text-white d-flex align-items-center justify-content-center" 
                                                     style="width: 45px; height: 45px; border-radius: 50%; border: 5px solid white; box-shadow: 0 0 0 1px #dee2e6;">
                                                    <span class="small fw-medium">{{ substr($customer->name, 0, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('customer.update', $customer->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="selected" value="{{ $customer->selected == 1 ? 0 : 1 }}">
                                            <button type="submit" class="btn btn-sm {{ $customer->selected == 1 ? 'btn-success' : 'btn-danger' }}">
                                                <i class="fas {{ $customer->selected == 1 ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                                {{ $customer->selected == 1 ? 'Đã kích hoạt' : 'Chưa kích hoạt' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                <small>
                    Hiển thị <strong>{{ $customers->firstItem() }}</strong> - <strong>{{ $customers->lastItem() }}</strong> trong tổng số <strong>{{ $customers->total() }}</strong> thương hiệu
                </small>
            </div>
            <div>
                {{ $customers->onEachSide(1)->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
    <script>
        function filterTable() {
            let input = document.getElementById("searchCustomer").value.toLowerCase();
            let rows = document.querySelectorAll("#searchCustomer tbody tr");
    
            rows.forEach(row => {
                let customer = row.cells[2].textContent.toLowerCase();
    
                if (customer.includes(input)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }
    </script>
@endsection
