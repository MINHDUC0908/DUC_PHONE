@extends('admin.layouts.app')
@section('content')
    <style>
            #orderStatusChart {
                max-width:400px;
                max-height:400px;
            }
            /* #monthlyRevenueChart {
                max-width:400px;
                max-height:400px;
            } */
    </style>
    <div class="container-fluid p-4">
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="card text-center card-gradient-primary">
                    <div class="card-body">
                        <i class="bi bi-people card-icon"></i>
                        <h5 class="card-title">Users</h5>
                        <p class="card-text">{{ $customer }}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center card-gradient-success">
                    <div class="card-body">
                        <i class="bi bi-boxes card-icon"></i>
                        <h5 class="card-title">Products</h5>
                        <p class="card-text">{{$product}}</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card text-center card-gradient-warning">
                    <div class="card-body">
                        <i class="bi bi-cart-check card-icon"></i>
                        <h5 class="card-title">Orders</h5>
                        <p class="card-text">{{ number_format($countOrder) }}</p>
                    </div>
                </div>
            </div>
            @php
                $total = 0;
                foreach ($order as $item) {
                    $total+=$item->total_price;
                }
            @endphp
            <div class="col-lg-3 col-md-6">
                <div class="card text-center card-gradient-danger">
                    <div class="card-body">
                        <i class="bi bi-cash-coin card-icon"></i>
                        <h5 class="card-title">Revenue</h5>
                        <p class="card-text">{{ number_format($total) }}₫</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <h1>
        Thống kê doanh thu hàng tháng
    </h1>
    <canvas id="monthlyRevenueChart" width="400" height="200">Thông kế doanh thu hàng tháng</canvas>
    <script>
        fetch('/api/statistics/monthly-revenue')
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => 'Tháng ' + item.month);
                const values = data.map(item => item.revenue);

                const ctx = document.getElementById('monthlyRevenueChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Doanh thu (VNĐ)',
                            data: values,
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
    </script>
    <h1 style="margin-top: 20px">
        Thống kê doanh thu hàng tuần
    </h1>
    <canvas id="weeklyRevenueChart" width="400" height="200"></canvas>
    <script>
        fetch('/api/statistics/weeklyRevenueStats')
            .then(response => response.json())
            .then(data => {
                const weeks = data.map(item => 'Week ' + item.week + ' - ' + item.year);
                const revenues = data.map(item => item.revenue);
    
                const ctx = document.getElementById('weeklyRevenueChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: weeks,
                        datasets: [{
                            label: 'Doanh thu tuần',
                            data: revenues,
                            borderColor: '#FF6384',
                            fill: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return 'Doanh thu: ' + tooltipItem.raw.toLocaleString() + ' VNĐ';
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    </script>  
        <h1 style="margin-top: 20px">
            Thống kê doanh thu hàng ngày
        </h1>
    <canvas id="dailyRevenueChart" width="400" height="200"></canvas>
    <script>
        fetch('/api/statistics/dailyRevenueStats')
            .then(response => response.json())
            .then(data => {
                const dates = data.map(item => item.date);
                const revenues = data.map(item => item.revenue);
    
                const ctx = document.getElementById('dailyRevenueChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: dates,
                        datasets: [{
                            label: 'Doanh thu ngày',
                            data: revenues,
                            borderColor: '#FF6384',
                            fill: false,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return 'Doanh thu: ' + tooltipItem.raw.toLocaleString() + ' VNĐ';
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    </script>      
    <h1 style="margin-top: 20px">
        Thống kê đơn hàng
    </h1>
    <canvas id="orderStatusChart" width="100" height="100"></canvas>
    <script>
        fetch('/api/statistics/orderStatusStats')
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => item.status);
                const values = data.map(item => item.count);

                const ctx = document.getElementById('orderStatusChart').getContext('2d');
                const orderStatusChart = new Chart(ctx, {
                    type: 'pie',  // Biểu đồ hình tròn
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0'],
                        }]
                    },
                    options: {
                        responsive: true,  // Thêm responsive để biểu đồ có thể điều chỉnh theo kích thước
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        return tooltipItem.label + ': ' + tooltipItem.raw + ' orders';
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => console.error('Error fetching data:', error));
    </script>
@endsection