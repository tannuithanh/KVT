@extends('Layout.app')
@section('style')
<link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" />
    <style>
        .filter-box {
            border: 1px solid #173e86; /* Màu border, có thể điều chỉnh */
            padding: 11px;
            margin-bottom: 20px; /* Khoảng cách với nội dung tiếp theo */
            border-radius: 5px; /* Bo góc cho khung */
        }
        .select2-container--default .select2-selection--single,
        .select2-selection .select2-selection--single {
            border: 1px solid #ced4da;
            border-radius: 0.30rem;
            height: calc(2.25rem + 2px);
            line-height: 1.5;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: calc(2.25rem + 2px);
        }

        .select2-container .select2-selection--single .select2-selection__rendered {
            padding-left: 0.75rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 2.25rem;
        }

        .select2-container--open .select2-dropdown {
            border-color: #ced4da;
        }

    </style>
@endsection
@section('title')
   Tồn kho
@endsection

@section('content')
<div class="pagetitle">
    <h1>Danh sách Tồn kho</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Trang chủ</a></li>
            <li class="breadcrumb-item active">Quản lý tồn kho</li>
        </ol>
    </nav>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="filter-box mt-3">
                        <div class="row">
                            <div class="col-sm-2">
                                <select class="form-select thuonghieu" aria-label="Default select example">
                                    <option value="">Chọn thương hiệu</option>
                                    <option value="1">Xe Bus</option>
                                    <option value="2">Xe Tải</option>
                                    <option value="3">Xe du lịch</option>
                                    <option value="4">Xe Royal</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select class="form-select phankhuc" aria-label="Default select example" disabled>
                                    <option selected="">Chọn phân khúc</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <select class="form-select duan" aria-label="Default select example" disabled>
                                    <option selected="">Chọn dự án</option>
                                </select>
                            </div>
                            <div class="col-sm-2" style="width: 10%">
                                <select class="donhang" aria-label="Default select example" disabled>
                                    <option selected="">Chọn đơn hàng</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" id="timkiemTonKho" class="btn btn-primary" style="margin-left: -5px">Tìm kiếm</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <label  id="labelThuongHieu">Thương Hiệu:...</label> | <span  id="labelPhanKhuc">Phân khúc:...</span> | <span  id="labelDuAn">Dự án:...</span>
                        <table class="table table-borderless table-bordered mt-1" id="bangTonKho">
                            <thead>
                                    <tr>
                                        <th style="text-align: center"  scope="col">Stt</th>

                                        <th style="text-align: center"  scope="col">Mã đơn hàng</th>
                                        <th style="text-align: center"  scope="col">Tên vật tư</th>
                                        <th style="text-align: center"  scope="col">Mã vật tư</th>
                                        <th style="text-align: center"  scope="col">ĐVT</th>
                                        <th style="text-align: center"  scope="col">số lượng tồn</th>
                                    </tr>
                            </thead>
                            <tbody>
                                    <tr>
                                        <td colspan="7" style="text-align: center">Vui lòng chọn dữ liệu</td>
                                    </tr>
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
<script src="{{asset('assets/js/select2.min.js')}}"></script>
{{-- LẤY PHÂN KHÚC --}}
    <script>
        $(document).ready(function(){
            $('.thuonghieu').change(function(){
                var phanKhucSelect = $('.phankhuc'); // Chọn select box thứ hai
                var duanSelect = $('.duan'); // Chọn select box thứ hai
                var donhangSelect = $('.donhang'); // Chọn select box thứ hai
                if($(this).val() != ''){
                    $.ajax({
                        url: "{{ route('slectedPhanKhuc') }}",
                        method: 'GET',
                        data: {thuongHieuId: $(this).val()},
                        success: function(data){
                            phanKhucSelect.empty(); // Xóa các option hiện tại
                            phanKhucSelect.prop('disabled', false); // Kích hoạt select box

                            // Thêm option mặc định
                            phanKhucSelect.append($('<option>', {
                                value: '',
                                text: 'Chọn phân khúc',
                                selected: true
                            }));

                            // Lặp qua và thêm các option mới từ dữ liệu trả về
                            $.each(data, function(index, value){
                                phanKhucSelect.append($('<option>', {
                                    value: value.id,
                                    text: value.name
                                }));
                            });
                        }
                    });
                } else {
                    // Nếu không chọn giá trị nào, xóa các option và disable select box
                    phanKhucSelect.empty(); // Xóa các option hiện tại
                    phanKhucSelect.prop('disabled', true); // Disable select box
                    // Thêm lại option mặc định
                    phanKhucSelect.append($('<option>', {
                        value: '',
                        text: 'Chọn phân khúc',
                        selected: true
                    }));
                    duanSelect.empty(); // Xóa các option hiện tại
                    duanSelect.prop('disabled', true); // Disable select box
                    // Thêm lại option mặc định
                    duanSelect.append($('<option>', {
                        value: '',
                        text: 'Chọn dự án',
                        selected: true
                    }));
                    donhangSelect.empty(); // Xóa các option hiện tại
                    donhangSelect.prop('disabled', true); // Disable select box
                    // Thêm lại option mặc định
                    donhangSelect.append($('<option>', {
                        value: '',
                        text: 'Chọn đơn hàng',
                        selected: true
                    }));
                }
            });
        });
    </script>
{{-- LẤY DỰ ÁN --}}
    <script>
        $(document).ready(function(){
            $('.phankhuc').change(function(){
                var segmentId = $(this).val();
                var projectSelect = $('.duan');
                projectSelect.empty(); // Xóa các option hiện tại ngay khi có sự thay đổi
                projectSelect.prop('disabled', true); // Mặc định là disable, cho đến khi có dữ liệu hợp lệ

                if(segmentId != ''){
                    $.ajax({
                        url: "{{ route('slectedDuAn') }}",
                        method: 'GET',
                        data: {segmentId: segmentId},
                        success: function(data){
                            if(data.length > 0){
                                projectSelect.prop('disabled', false); // Kích hoạt select box khi có dữ liệu
                                projectSelect.append($('<option>', {
                                    value: '',
                                    text: 'Chọn dự án',
                                    selected: true
                                }));
                                $.each(data, function(index, project){
                                    projectSelect.append($('<option>', {
                                        value: project.id,
                                        text: project.name
                                    }));
                                });
                            } else {
                                // Thêm option "Không có dữ liệu" nếu không có dự án nào trong phân khúc
                                projectSelect.append($('<option>', {
                                    value: '',
                                    text: 'Không có dữ liệu',
                                    selected: true
                                }));
                            }
                        }
                    });
                } else {
                    // Nếu không chọn phân khúc nào, reset select box với option mặc định
                    projectSelect.append($('<option>', {
                        value: '',
                        text: 'Chọn dự án',
                        selected: true
                    }));
                }
            });
        });
    </script>
{{-- LẤY ĐƠN HÀNG --}}
    <script>
        $(document).ready(function(){
            // Khởi tạo Select2 cho select box đơn hàng ban đầu
            $('.donhang').select2({
                placeholder: "Chọn đơn hàng",
                allowClear: true,
                width: '100%' // Tùy chỉnh chiều rộng
            });

            $('.duan').change(function(){
                var projectId = $(this).val(); // Lấy ID của dự án được chọn
                var orderSelect = $('.donhang'); // Chọn select box đơn hàng
                // Làm mới Select2 bằng cách xóa các option hiện có
                orderSelect.empty().append('<option></option>'); // Thêm một option trống để giữ placeholder

                if(projectId != ''){
                    $.ajax({
                        url: "{{ route('selectedDonHang') }}", // Sửa lại route cho đúng nếu cần
                        method: 'GET',
                        data: {projectId: projectId}, // Gửi ID dự án như một tham số
                        success: function(data){
                            if(data.length > 0){
                                // Thêm các option mới vào select box từ dữ liệu nhận được
                                $.each(data, function(index, order){
                                    var newOption = new Option(order.sodonhang, order.id, false, false);
                                    orderSelect.append(newOption).trigger('change');
                                });

                                // Sau khi cập nhật các option, khởi tạo lại Select2
                                orderSelect.prop('disabled', false).select2({
                                    placeholder: "Chọn đơn hàng",
                                    allowClear: true,
                                    width: '100%'
                                });
                            } else {
                                // Khởi tạo lại Select2 mà không có option mới nếu không có đơn hàng
                                orderSelect.prop('disabled', true).select2({
                                    placeholder: "Không có dữ liệu",
                                    allowClear: true,
                                    width: '100%'
                                });
                            }
                        }
                    });
                } else {
                    // Khởi tạo lại Select2 với placeholder mặc định nếu không chọn dự án
                    orderSelect.select2({
                        placeholder: "Chọn đơn hàng",
                        allowClear: true,
                        width: '100%'
                    });
                }
            });
        });
    </script>
{{-- TÌM KIẾM THÔNG TIN TỒN KHO --}}
    <script>
        $(document).ready(function(){
            $('#timkiemTonKho').click(function(event){
                event.preventDefault(); // Ngăn chặn hành vi mặc định của form submit

                // Lấy giá trị từ các select box, nếu disabled thì gửi giá trị rỗng
                var thuongHieuId = $('.thuonghieu').prop('disabled') ? '' : $('.thuonghieu').val();
                var phanKhucId = $('.phankhuc').prop('disabled') ? '' : $('.phankhuc').val();
                var duAnId = $('.duan').prop('disabled') ? '' : $('.duan').val();
                var donHangId = $('.donhang').prop('disabled') ? '' : $('.donhang').val();

                // Kiểm tra nếu phân khúc rỗng
                if(!phanKhucId && !duAnId) {
                    Swal.fire({ // Sử dụng SweetAlert2
                        title: 'Thông báo',
                        text: 'Vui lòng chọn phân khúc và dự án!',
                        icon: 'warning',
                        confirmButtonText: 'Đóng'
                    });
                    return; // Dừng thực thi thêm nếu không chọn phân khúc
                }else if(!phanKhucId){
                    Swal.fire({ // Sử dụng SweetAlert2
                        title: 'Thông báo',
                        text: 'Vui lòng chọn phân khúc!',
                        icon: 'warning',
                        confirmButtonText: 'Đóng'
                    });
                    return; // Dừng thực thi thêm nếu không chọn phân khúc
                }else if(!duAnId){
                    Swal.fire({ // Sử dụng SweetAlert2
                        title: 'Thông báo',
                        text: 'Vui lòng chọn dự án!',
                        icon: 'warning',
                        confirmButtonText: 'Đóng'
                    });
                    return; // Dừng thực thi thêm nếu không chọn phân khúc
                }

                $.ajax({
                    url: "{{ route('listTonKho') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        thuongHieuId: thuongHieuId,
                        phanKhucId: phanKhucId,
                        duAnId: duAnId,
                        donHangId: donHangId,
                    },
                    success: function(response) {
                        $('#labelThuongHieu').text('Thương Hiệu: ' + response.brand.name);
                        $('#labelPhanKhuc').text('Phân khúc: ' + response.segment.name);
                        $('#labelDuAn').text('Dự án: ' + response.project.name);

                        var tableBody = $("#bangTonKho tbody");
                        tableBody.empty();

                        if (response.project.orders.length > 0) {
                            var orderMap = new Map(); // Sử dụng Map để nhóm các vật tư theo số đơn hàng

                            // Lặp qua và nhóm các vật tư theo số đơn hàng
                            response.project.orders.forEach(function(order) {
                                order.supplies.forEach(function(supply) {
                                    if (!orderMap.has(order.sodonhang)) {
                                        orderMap.set(order.sodonhang, {
                                            sodonhang: order.sodonhang,
                                            supplies: []
                                        });
                                    }
                                    orderMap.get(order.sodonhang).supplies.push(supply);
                                });
                            });

                            // Tạo hàng cho mỗi nhóm đơn hàng
                            var indexstt = 1;
                            orderMap.forEach(function(order, sodonhang) {
                                order.supplies.forEach(function(supply, index) {
                                    var rowSpan = index === 0 ? order.supplies.length : 0; // Chỉ set rowspan cho hàng đầu tiên của mỗi nhóm
                                    var row = `<tr>
                                        ${rowSpan ? `<td rowspan="${rowSpan}" style="text-align: center;vertical-align: middle;">${indexstt++}</td>
                                        <td rowspan="${rowSpan}" style="text-align: center;vertical-align: middle;">${sodonhang}</td>` : ''}
                                        <td style="text-align: center">${supply.tenvattu}</td>
                                        <td style="text-align: center">${supply.maso}</td>
                                        <td style="text-align: center">${supply.donvitinh}</td>
                                        <td style="text-align: center">${supply.soluongTon}</td>
                                    </tr>`;
                                    tableBody.append(row);
                                });
                            });
                        } else {
                            tableBody.append('<tr><td colspan="7" style="text-align: center">Không có dữ liệu</td></tr>');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Xử lý lỗi nếu có
                        console.error(error);
                    }
                });
            });
        });
    </script>


@endsection
