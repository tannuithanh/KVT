@extends('Layout.app')

@section('title')
    Trang chủ | Quản lý kho
@endsection

@section('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/Dashboard/trangChuKeHoach.css') }}">
    <style>
        .chart-container {
            position: relative;
            height: 60vh;
            width: 40vw;
        }
    </style>
@endsection

@section('content')
<div class="pagetitle">
    <h1>Dashboard kế hoạch</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashBoard')}}">Trang chủ</a></li>
            <li class="breadcrumb-item">Dashboard P.Kế Hoạch</li>
        </ol>
    </nav>
</div>
<section class="section">
    <div class="row">
        <!-- Thẻ card chứa thống kê đơn hàng -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header card-header-custom">Thống kê đơn hàng</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-4 mt-3">
                            <div class="stat-box total-orders">
                                <span class="icon"><i class="fas fa-box"></i></span>
                                <h3>{{ $totalOrders }}</h3>
                                <p>Tổng số đơn hàng</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4 mt-3">
                            <div class="stat-box completed-orders">
                                <span class="icon"><i class="fas fa-check"></i></span>
                                <h3>{{ $completedOrders }}</h3>
                                <p>Đơn hàng đã hoàn thành</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4 mt-3">
                            <div class="stat-box pending-orders">
                                <span class="icon"><i class="fas fa-hourglass-half"></i></span>
                                <h3>{{ $pendingOrders }}</h3>
                                <p>Đơn hàng chờ nhận hàng</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Thẻ card chứa bảng và biểu đồ -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header card-header-custom">Danh mục vật tư cần tạo</div>
                <div class="card-body" style="height: 420px;">
                    <div class="table-responsive mt-2">
                        <table class="table table-bordered mt-2">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên danh mục vật tư</th>
                                    <th>Nhà cung cấp</th>
                                    <th>Số lượng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($catalogsCanCreateOrders as $index => $catalog)
                                    <tr>
                                        <td style="text-align: center">{{ $index + 1 }}</td>
                                        <td style="text-align: center">{{ $catalog->name }}</td>
                                        <td style="text-align: center">{{ $catalog->provider_info->name }}</td>
                                        <td style="text-align: center">{{ $catalog->supplies->count() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

        <!-- Thẻ card chứa biểu đồ -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header card-header-custom">
                    Biểu đồ thống kê

                </div>
                <div class="card-body" style="height: 420px;">
                    <div class="float-right">
                        <button class="btn btn-outline-primary btn-sm" onclick="updateChart('week')">Tuần</button>
                        <button class="btn btn-outline-primary btn-sm" onclick="updateChart('month')">Tháng</button>
                        <button class="btn btn-outline-primary btn-sm" onclick="updateChart('year')">Năm</button>
                    </div>
                    <div class="chart-container">
                        <canvas id="chartOrder"></canvas>
                    </div>
                </div>
            </div>
        </div>



    </div>
</section>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var chartCanvas = document.getElementById('chartOrder');
            var ctx = chartCanvas.getContext('2d');

            var orderChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Đơn hàng đã hoàn thành',
                        data: [],
                        backgroundColor: '#28a745', // Màu xanh lá cho đã hoàn thành
                        borderColor: '#1e7e34', // Biên xanh đậm
                        borderWidth: 1
                    },
                    {
                        label: 'Đơn hàng đang thực hiện',
                        data: [],
                        backgroundColor: '#ffc107', // Màu vàng cho đang thực hiện
                        borderColor: '#e0a800', // Biên vàng đậm
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                        position: 'top',
                    },
                        title: {
                        display: true,
                        text: 'Tỷ lệ đơn hàng'
                    }
                }
            }
            });

            window.updateChart = function(timeFrame) {
                var url = '{{ route('apiOrder') }}?timeframe=' + timeFrame;

                fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    var labels = data.labels;
                    var completedOrdersData = data.completedOrders;
                    var pendingOrdersData = data.pendingOrders;

                    orderChart.data.labels = labels;
                    orderChart.data.datasets[0].data = completedOrdersData;
                    orderChart.data.datasets[1].data = pendingOrdersData;
                    orderChart.update();
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
            };

            // Initial chart update
            updateChart('week');
        });
    </script>
@endsection
