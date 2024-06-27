@extends('Layout.app')

@section('style')
    <style>
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .info-card {
            text-align: center;
            padding: 20px 0;
        }
        .card-header {
            background-color: #4e73df;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        .card-title {
            font-weight: bold;
        }
        .btn-custom {
            margin: 5px;
        }
        .table-custom thead th {
            background-color: #4e73df;
            color: white;
        }
    </style>
@endsection

@section('title')
    Trang chủ | Quản lý kho
@endsection

@section('content')
<div class="pagetitle">
    <h1>Trang chủ</h1>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h6>Biểu đồ thống kê</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="btn btn-sm btn-outline-secondary btn-custom">Xuất dữ liệu</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle btn-custom">
                            Đơn hàng
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle btn-custom">
                            Tháng
                        </button>
                    </div>
                    <div class="row">
                        <!-- Biểu đồ đường -->
                        <div class="col-md-6 mb-4">
                            <h5 class="card-title">Biểu đồ đường</h5>
                            <canvas id="lineChart" width="300" height="150"></canvas>
                        </div>
                        <!-- Biểu đồ tròn -->
                        <div class="col-md-6 mb-4">
                            <h5 class="card-title">Biểu đồ tròn</h5>
                            <canvas id="pieChart" width="300" height="150">></canvas>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Bảng danh mục vật tư -->
                        <div class="col-md-12 mb-4">
                            <h5 class="card-title">Danh mục vật tư</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-custom">
                                    <thead>
                                        <tr>
                                            <th>Stt</th>
                                            <th>Tên DMVT</th>
                                            <th>Chi phí</th>
                                            <th>Ghi chú</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Vật tư 1</td>
                                            <td>Bus</td>
                                            <td>không có</td>
                                            <td>hoàn thành</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Vật tư 2</td>
                                            <td>Bus</td>
                                            <td>không có</td>
                                            <td>chưa hoàn thành</td>
                                        </tr>
                                        <!-- More rows... -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Bảng danh sách nhân viên kho -->
                        <div class="col-md-12 mb-4">
                            <h5 class="card-title">Danh sách nhân viên kho</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover table-custom">
                                    <thead>
                                        <tr>
                                            <th>Stt</th>
                                            <th>Tên dự án</th>
                                            <th>Chi phí</th>
                                            <th>Ghi chú</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>Vật tư 1</td>
                                            <td>Bus</td>
                                            <td>không có</td>
                                            <td>hoàn thành</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>Vật tư 2</td>
                                            <td>Bus</td>
                                            <td>không có</td>
                                            <td>chưa hoàn thành</td>
                                        </tr>
                                        <!-- More rows... -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal kiểm tra chất lượng -->
        <div class="modal fade" id="qualityCheckModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Đơn hàng cần kiểm tra chất lượng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group">
                            @foreach ($ordersNeedQualityCheck as $order)
                                <li class="list-group-item">Đơn hàng số: {{ $order->sodonhang }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <a href="{{route('checkQuality')}}" class="btn btn-primary" id="checkQualityBtn">Kiểm tra</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    @if ($showPopup && !$ordersNeedQualityCheck->isEmpty())
        window.onload = function() {
            $('#qualityCheckModal').modal('show');
        }
    @endif
</script>
<script>
    var ctxLine = document.getElementById('lineChart').getContext('2d');
    var lineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'],
            datasets: [{
                label: 'Số lượng đơn hàng',
                data: [12, 19, 3, 5, 2, 3, 7, 6, 7, 7, 7, 7, 4],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    var ctxPie = document.getElementById('pieChart').getContext('2d');
    var pieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: ['Danh mục vật tư đã hoàn thành', 'Danh mục vật tư chưa hoàn thành'],
            datasets: [{
                label: 'Số lượng danh mục vật tư',
                data: [12, 19],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    enabled: true
                },
                datalabels: {
                    color: 'black',
                    formatter: (value, context) => {
                        return value;
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
</script>
@endsection
