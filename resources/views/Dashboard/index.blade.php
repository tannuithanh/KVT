@extends('Layout.app')
@section('style')
<style>
    .table th, .table td {
        text-align: center;
        vertical-align: middle;
    }
</style>
@endsection
@section('title')
    Trang chủ | Quản lý kho
@endsection

@section('content')
    <div class="pagetitle">
        <h1>Trang chủ</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active">Bảng điều khiển</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-8">
                <div class="row">
                    <!-- ======= NHẬP KHO ======= -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card sales-card">

                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown" aria-expanded="false"><i
                                            class="bi bi-three-dots"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                        <li class="dropdown-header text-start">
                                            <h6>LỌC</h6>
                                        </li>

                                        <li><a class="dropdown-item" href="#">Hôm nay</a></li>
                                        <li><a class="dropdown-item" href="#">Tháng này</a></li>
                                        <li><a class="dropdown-item" href="#">Năm nay</a></li>
                                    </ul>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title">Nhập kho <span>| Hôm nay</span></h5>

                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="bi bi-cart-check"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6 style="display: inline;">145</h6>
                                            <h6 style="display: inline;">sản phẩm</h6>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        </div>
                    <!-- ======= XUẤT KHO ======= -->
                        <div class="col-xxl-4 col-md-6">
                            <div class="card info-card revenue-card">

                                <div class="filter">
                                    <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                            class="bi bi-three-dots"></i></a>
                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                        <li class="dropdown-header text-start">
                                            <h6>LỌC</h6>
                                        </li>

                                        <li><a class="dropdown-item" href="#">Hôm nay</a></li>
                                        <li><a class="dropdown-item" href="#">Tháng này</a></li>
                                        <li><a class="dropdown-item" href="#">Năm nay</a></li>
                                    </ul>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title">Xuất kho <span>| Hôm nay</span></h5>

                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                                            style="color: red; background-color: rgb(255, 219, 219)">
                                            <i class="bi bi-cart-dash"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6 style="display: inline;">12</h6>
                                            <h6 style="display: inline;">sản phẩm</h6>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    <!-- ======= TỒN KHO ======= -->
                        <div class="col-xxl-4 col-xl-12">
                            <div class="card info-card revenue-card">

                                <div class="card-body">
                                    <h5 class="card-title">Tồn kho</h5>

                                    <div class="d-flex align-items-center">
                                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                                            style="color: rgb(255, 136, 0); background-color: rgb(252, 234, 219)">
                                            <i class="bi bi-card-checklist"></i>
                                        </div>
                                        <div class="ps-3">
                                            <h6 style="display: inline;">12</h6>
                                            <h6 style="display: inline;">sản phẩm</h6>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                </div>
                <!-- ======= BIỂU ĐỒ HÌNH LINE ======= -->
                    <div class="col-lg-12">
                        <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Biểu đồ xuất nhập tồn</h5>
                            <div id="lineChart"></div>
                        </div>
                        </div>
                    </div>
                <!-- ======= BẢNG THEO DÕI XUẤT NHẬP TỒN ======= -->
                    <div class="col-lg-12">
                        <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Trạng thái kho trong ngày</h5>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th scope="col">Stt</th>
                                        <th scope="col">Tên thương hiệu</th>
                                        <th scope="col">Tên dự án</th>
                                        <th scope="col">Số lượng nhập kho</th>
                                        <th scope="col">Số lượng xuất kho</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr >
                                        <td>1</td>
                                        <td>Xe Bus</td>
                                        <td>BT2111</td>
                                        <td>28</td>
                                        <td>28</td>
                                    </tr>
                                    <tr >
                                        <td>2</td>
                                        <td>Xe Bus</td>
                                        <td>BT2111</td>
                                        <td>28</td>
                                        <td>28</td>
                                    </tr>
                                    <tr >
                                        <td>3</td>
                                        <td>Xe Bus</td>
                                        <td>BT2111</td>
                                        <td>28</td>
                                        <td>28</td>
                                    </tr>
                                    <tr >
                                        <td>2</td>
                                        <td>Xe Bus</td>
                                        <td>BT2111</td>
                                        <td>28</td>
                                        <td>28</td>
                                    </tr>
                                    <tr >
                                        <td>3</td>
                                        <td>Xe Bus</td>
                                        <td>BT2111</td>
                                        <td>28</td>
                                        <td>28</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
            </div>
            <div class="col-lg-4">
                    <!-- ======= BIỂU ĐỒ HÌNH TRÒN ======= -->
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Biểu đồ vật tư theo thương hiệu</h5>
                                <div id="pieChart"></div>
                            </div>
                        </div>
                    <!-- ======= BẢNG THÔNG TIN CHI TIẾT ======= -->
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Thông tin chi tiết</h5>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th scope="col">Stt</th>
                                            <th scope="col">Tên thương hiệu</th>
                                            <th scope="col">Tổng dự án</th>
                                            <th scope="col">Tổng vật tư</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr >
                                            <td >1</td>
                                            <td style="background-color: #ff4962; font-weight: bold;color: white">Xe Bus</td>
                                            <td >2</td>
                                            <td >28</td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td style="background-color: #00e49b; font-weight: bold;color: white">Xe Tải</td>
                                            <td>1</td>
                                            <td>35</td>
                                        </tr>
                                        <tr>
                                            <td>3</td>
                                            <td style="background-color: #2a88f5; font-weight: bold;color: white">Xe Du Lịch</td>
                                            <td>1</td>
                                            <td>45</td>
                                        </tr>
                                        <tr>
                                            <td>4</td>
                                            <td style="background-color: #7e54cb; font-weight: bold;color: white">Xe Royal</td>
                                            <td>1</td>
                                            <td>34</td>
                                        </tr>
                                        <tr>
                                            <td>5</td>
                                            <td style="background-color: #feb43b; font-weight: bold;color: white">Xe Hai Bánh</td>
                                            <td>2</td>
                                            <td>34</td>
                                        </tr>
                                        <tr>
                                            <td>6</td>
                                            <td style="background-color: #00d7e7; font-weight: bold;color: white">Xe Nông Nghiệp</td>
                                            <td>1</td>
                                            <td>34</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
            </div>
        </div>

    </section>
@endsection
@section('script')
<script src="{{asset('assets/js/apexcharts.js')}}"></script>
<!-- ======= BIỂU ĐỒ HÌNH LINE ======= -->
    <script>
        var options = {
            chart: {
                type: 'line',
                height: 350,
                zoom: {
                    enabled: true,
                    type: 'x', // Cho phép zoom theo trục X
                    autoScaleYaxis: true // Tự động điều chỉnh tỷ lệ trục Y khi zoom
                },
                toolbar: {
                    autoSelected: 'zoom' // Chọn công cụ zoom mặc định
                }
            },
            stroke: {
                curve: 'smooth', // Làm mượt các đường
                width: 2 // Độ rộng của đường line
            },
            colors: ['#4154f1', '#FF0000', '#FFA500'],
            series: [{
                name: 'Nhập kho',
                data: generateDayWiseTimeSeries(new Date('11 Feb 2017 GMT').getTime(), 20, {
                    min: 30,
                    max: 90
                })
            }, {
                name: 'Xuất kho',
                data: generateDayWiseTimeSeries(new Date('11 Feb 2017 GMT').getTime(), 20, {
                    min: 30,
                    max: 90
                })
            }, {
                name: 'Tồn kho',
                data: generateDayWiseTimeSeries(new Date('11 Feb 2017 GMT').getTime(), 20, {
                    min: 30,
                    max: 90
                })
            }],
            xaxis: {
                type: 'datetime'
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy'
                }
            },
            // Bạn có thể thêm các tùy chọn khác như markers, dataLabels, ...
        };

        // Hàm tạo dữ liệu giả lập theo ngày
        function generateDayWiseTimeSeries(baseval, count, yrange) {
            var i = 0;
            var series = [];
            while (i < count) {
                var x = baseval;
                var y = Math.floor(Math.random() * (yrange.max - yrange.min + 1)) + yrange.min;

                series.push([x, y]);
                baseval += 86400000; // thêm 1 ngày trong milliseconds
                i++;
            }
            return series;
        }

        var chart = new ApexCharts(document.querySelector("#lineChart"), options);
        chart.render();
    </script>
<!-- ======= BIỂU ĐỒ HÌNH TRÒN ======= -->
    <script>
        var optionsPie = {
        chart: {
            type: 'pie' // Thay đổi từ 'bar' sang 'pie'
        },
        series: [30, 40, 25, 50, 42, 35], // Sử dụng dữ liệu từ biểu đồ hình cột
        labels: ['Xe Bus', 'Xe Tải', 'Xe Du lịch', 'Xe Royal', 'Xe Hai Bánh', 'Xe Nông nghiệp'], // Sử dụng danh mục từ biểu đồ hình cột
        colors: ['#FF4560', '#00E396', '#008FFB', '#775DD0', '#FEB019', '#00D9E9']
        };

        var chartPie = new ApexCharts(document.querySelector("#pieChart"), optionsPie);
        chartPie.render();
    </script>



@endsection
