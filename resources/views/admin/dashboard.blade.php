@extends('admin.layouts.app')
@section('content')
    <style>
        .dashboard-card {
            border-radius: 12px;
            padding: 20px;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .card-icon {
            font-size: 2rem;
            opacity: 0.7;
        }
        .card-gradient-primary { background: linear-gradient(45deg, #4facfe, #00f2fe); }
        .card-gradient-success { background: linear-gradient(45deg, #42e695, #3bb2b8); }
        .card-gradient-warning { background: linear-gradient(45deg, #ff9a44, #fc6076); }
        .card-gradient-danger { background: linear-gradient(45deg, #f7797d, #fbd786); }

        /* Đảm bảo tất cả các biểu đồ có kích thước giống nhau */
        .chart-container {
            width: 100%;
            max-width: 600px; 
            margin: auto;
            padding: 20px 0;
        }

        canvas {
            width: 100% !important;
            height: 300px !important; 
        }

        .card {
            min-height: 400px; 
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        h2, h1 {
            text-align: center;
            margin-top: 40px;
        }
        canvas#orderStatusChart {
            max-width: 400px; 
            max-height: 400px; 
            margin: auto;
        }

    </style>
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
    <div class="container-fluid p-4">
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card card-gradient-primary">
                    <i class="bi bi-people card-icon"></i>
                    <div>
                        <h5>Users</h5>
                        <p class="fs-4 fw-bold">{{ $customer }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card card-gradient-success">
                    <i class="bi bi-boxes card-icon"></i>
                    <div>
                        <h5>Products</h5>
                        <p class="fs-4 fw-bold">{{$product}}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card card-gradient-warning">
                    <i class="bi bi-cart-check card-icon"></i>
                    <div>
                        <h5>Orders</h5>
                        <p class="fs-4 fw-bold">{{ number_format($countOrder) }}</p>
                    </div>
                </div>
            </div>
            @php
                $total = array_sum(array_column($order->toArray(), 'total_price'));
            @endphp
            <div class="col-lg-3 col-md-6">
                <div class="dashboard-card card-gradient-danger">
                    <i class="bi bi-cash-coin card-icon"></i>
                    <div>
                        <h5>Revenue</h5>
                        <p class="fs-4 fw-bold">{{ number_format($total) }}₫</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ doanh thu -->
    <div class="container mt-4">
        <h2 class="text-center mb-4">Thống kê doanh thu</h2>
        <div class="row">
            <div class="col-lg-6 col-md-12">
                <div class="card shadow p-3 mb-4">
                    <h5 class="text-center">Doanh thu theo tháng</h5>
                    <canvas id="monthlyRevenueChart"></canvas>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="card shadow p-3 mb-4">
                    <h5 class="text-center">Doanh thu theo tuần</h5>
                    <canvas id="weeklyRevenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-12">
                <div class="card shadow p-3 mb-4">
                    <h5 class="text-center">Doanh thu theo ngày</h5>
                    <canvas id="dailyRevenueChart"></canvas>
                </div>
            </div>
            <div class="col-lg-6 col-md-12">
                <div class="card shadow p-3 mb-4">
                    <h5 class="text-center">Thống kê đơn hàng</h5>
                    <canvas id="orderStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Biểu đồ JS -->
    <script>
        function fetchChartData(url, callback) {
            fetch(url)
                .then(response => response.json())
                .then(callback)
                .catch(error => console.error('Lỗi tải dữ liệu:', error));
        }

        // Doanh thu theo tháng
        fetchChartData('/api/statistics/monthly-revenue', data => {
            new Chart(document.getElementById('monthlyRevenueChart'), {
                type: 'bar',
                data: {
                    labels: data.map(item => 'Tháng ' + item.month),
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: data.map(item => item.revenue),
                        backgroundColor: '#4facfe',
                    }]
                },
                options: { responsive: true }
            });
        });

        // Doanh thu theo tuần
        fetchChartData('/api/statistics/weeklyRevenueStats', data => {
            new Chart(document.getElementById('weeklyRevenueChart'), {
                type: 'line',
                data: {
                    labels: data.map(item => 'Tuần ' + item.week + ' - ' + item.year),
                    datasets: [{
                        label: 'Doanh thu tuần',
                        data: data.map(item => item.revenue),
                        borderColor: '#FF6384',
                        fill: false,
                    }]
                },
                options: { responsive: true }
            });
        });

        // Doanh thu theo ngày
        fetchChartData('/api/statistics/dailyRevenueStats', data => {
            new Chart(document.getElementById('dailyRevenueChart'), {
                type: 'line',
                data: {
                    labels: data.map(item => item.date),
                    datasets: [{
                        label: 'Doanh thu ngày',
                        data: data.map(item => item.revenue),
                        borderColor: '#36A2EB',
                        fill: false,
                    }]
                },
                options: { responsive: true }
            });
        });

        // Thống kê đơn hàng (Biểu đồ tròn)
        fetchChartData('/api/statistics/orderStatusStats', data => {
            new Chart(document.getElementById('orderStatusChart'), {
                type: 'pie',
                data: {
                    labels: data.map(item => item.status),
                    datasets: [{
                        data: data.map(item => item.count),
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                    }]
                },
                options: { 
                    responsive: true, 
                    maintainAspectRatio: false, 
                    aspectRatio: 1 
                }
            });
        });
    </script>
@endsection
