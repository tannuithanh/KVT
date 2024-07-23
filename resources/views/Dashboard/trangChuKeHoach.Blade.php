@extends('Layout.app')

@section('title')
    Trang chủ | Quản lý kho
@endsection

@section('style')
    <style>
        .card {
            border-radius: 0.25rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }
        .card-header {
            padding: 0.75rem 1.25rem;
            background-color: #047ef8;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }
        .card-body {
            padding: 1.25rem;
        }
        .table-custom {
            width: 100%;
            margin-bottom: 1rem;
            border-collapse: collapse;
        }
        .table-custom th, .table-custom td {
            padding: 0.75rem;
            border-top: 1px solid #dee2e6;
        }
        .table-custom thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .form-select {
            width: 100%;
            padding: 0.375rem 1.75rem 0.375rem 0.75rem;
            background-size: 8px 10px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            appearance: none;
        }
    </style>
    <style>
        .text-success {
            color: green;
        }
        .text-danger {
            color: red;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/choices.min.css')}}">
@endsection

@section('content')
<div class="pagetitle">
    <h1>Trang chủ</h1>
</div>
<section class="section">
    <div class="row">
        <!-- Card danh mục vật tư cần lên đơn hàng -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 style="font-weight: 500; color: white;">Danh mục vật tư cần lên đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered danhMucDangCho">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên danh mục</th>
                                    <th>Nhà cung cấp</th>
                                    <th>Số lượng vật tư</th>
                                    <th>Tình trạng</th>
                                </tr>
                            </thead>
                            <tbody id="material-category-table-body">
                                @foreach($catalogsCanCreateOrders as $index => $catalog)
                                <tr>
                                    <td style="text-align: center">{{ $index + 1 }}</td>
                                    <td style="text-align: center">{{ $catalog->name }}</td>
                                    <td style="text-align: center">{{ $catalog->provider_info ? $catalog->provider_info->provider->name : 'Không có thông tin nhà cung cấp' }}</td>
                                    <td style="text-align: center">{{ $catalog->supplies->count() }}</td>
                                    <td style="text-align: center">{{ $catalog->description }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card đơn hàng của dự án -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 style="font-weight: 500; color: white;">Đơn hàng của dự án</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <label for="project-orders-select" class="form-label">Chọn dự án</label>
                        <select id="project-orders-select" class="form-select">
                            <!-- Các tùy chọn sẽ được thêm vào đây -->
                        </select>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="results-container">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>DMVT</th>
                                    <th>Số đơn hàng</th>
                                    <th>Số vật tư</th>
                                    <th>Ngày tạo</th>
                                    <th>Tình trạng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <td colspan="6" style="text-align: center">Vui lòng chọn dự án</td>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection
@section('script')
<script src="{{asset('assets/js/choices.min.js')}}"></script>
<!-- HIỂN THỊ DỰ ÁN TRONG SELECT -->
    <script>
        $(document).ready(function() {
            const selectElement = document.getElementById('project-orders-select');
            const choices = new Choices(selectElement, {
                searchEnabled: true,
                placeholderValue: 'Chọn dự án...',
                noResultsText: 'Không tìm thấy kết quả'
            });

            // Lấy dữ liệu dự án từ backend
            const projects = @json($projects);

            // Chuyển đổi dữ liệu dự án thành định dạng phù hợp cho Choices.js
            const projectChoices = projects.map(project => ({
                value: project.id,
                label: project.name
            }));

            // Thêm các tùy chọn vào select bằng cách sử dụng choices.setChoices
            choices.setChoices(projectChoices, 'value', 'label', false);

            // Xử lý sự kiện khi thay đổi dự án
            $('#project-orders-select').on('change', function() {
                const projectId = $(this).val();
                if (projectId) {
                    $.ajax({
                        url: "{{route('orderByDashboard')}}",
                        method: 'GET',
                        data: { project_id: projectId },
                        dataType: 'json',
                        success: function(data) {
                            console.log(data);
                            if (data.error) {
                                console.error(data.error);
                                return;
                            }

                            // Xóa dữ liệu hiện tại
                            $('#results-container tbody').empty();
                            let stt = 1;
                            // Hiển thị dữ liệu mới
                            data.catalogs.forEach(catalogData => {
                                const catalog = catalogData.catalog;
                                const orders = catalogData.orders;

                                if (orders.length > 0) {
                                    orders.forEach(order => {
                                        let statusText = order.isEqual ? 'Hoàn thành' : 'Chưa hoàn thành';
                                        let statusClass = order.isEqual ? 'text-success' : 'text-danger';
                                        $('#results-container tbody').append(`
                                            <tr>
                                                <td style="text-align: center">${stt++}</td>
                                                <td style="text-align: center">${catalog.name}</td>
                                                <td style="text-align: center">${order.sodonhang}</td>
                                                <td style="text-align: center">${order.total_quantity}</td>
                                                <td style="text-align: center">${order.ngaytaophieu}</td>
                                                <td style="text-align: center" class="${statusClass}">${statusText}</td>
                                            </tr>
                                        `);
                                    });
                                }
                            });
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error('Error:', textStatus, errorThrown);
                        }
                    });
                }
            });
        });
    </script>
<!-- CHANGE DỰ ÁN TRONG SELECT -->
@endsection
