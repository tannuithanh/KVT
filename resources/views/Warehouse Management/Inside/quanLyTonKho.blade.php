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
                  <h5 class="card-title">Quản lý tồn kho</h5>
                            <div class="filter-box mt-1">
                                <div class="row">
                                    <div class="col-6 col-sm-2">
                                        <select class="form-select thuongHieuDonHang" aria-label="Default select example">
                                            <option value="">Chọn thương hiệu</option>
                                            <option value="1">Xe Bus</option>
                                            <option value="2">Xe Tải</option>
                                            <option value="3">Xe du lịch</option>
                                            <option value="4">Xe Royal</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-sm-2">
                                        <select class="form-select phanKhucDonHang" aria-label="Default select example" disabled>
                                            <option selected="">Chọn phân khúc</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-sm-2">
                                        <select class="form-select duAnDonHang" aria-label="Default select example" disabled>
                                            <option selected="">Chọn dự án</option>
                                        </select>
                                    </div>
                                    <div class="col-6 col-sm-2">
                                        <select class="donHangChiTiet" aria-label="Default select example">
                                            <option selected="">Chọn đơn hàng</option>
                                            @foreach($orders as $order)
                                                <option value="{{ $order->id }}">{{ $order->sodonhang }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-6 col-sm-2">
                                        <button type="submit" id="timkiemTonKhoDonHang" class="btn btn-primary" style="margin-left: -5px">Tìm kiếm</button>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <label  id="labelThuongHieuDonHang">Thương Hiệu:...</label> | <span  id="labelPhanKhucDonHang">Phân khúc:...</span> | <span  id="labelDuAnDonHang">Dự án:...</span>
                                <table class="table table-borderless table-bordered table-hover mt-1" style="max-height: 400px;" id="bangTonKhoTheoDonHang" >
                                    <thead>
                                            <tr>
                                                <th style="text-align: center"  scope="col">Stt</th>

                                                <th style="text-align: center"  scope="col">Đơn hàng</th>
                                                <th style="text-align: center"  scope="col">Tổng</th>
                                                <th style="text-align: center"  scope="col">Nhập</th>
                                                <th style="text-align: center"  scope="col">Xuất</th>
                                                <th style="text-align: center"  scope="col">Tồn</th>
                                            </tr>
                                    </thead>
                                    <tbody>
                                            <tr>
                                                <td colspan="10" style="text-align: center">Vui lòng chọn dữ liệu</td>
                                            </tr>
                                    </tbody>
                                </table>
                        </div>
                    </div>
              </div>
        </div>
    </div>
</section>
<!-- MODAL VẬT TƯ CHI TIẾT -->
    <div class="modal fade" id="danhMucVatTuChiTiet" tabindex="-1">
        <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Vật tư chi tiết</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="filter-box mt-3">
                    <div class="row">
                        <div class="col-6 col-md-2" >
                            <select class="tenvattuchitiet" aria-label="Default select example" >
                                <option selected="">Tên vật tư</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2" >
                            <select class="masochitiet" aria-label="Default select example" >
                                <option selected="">Mã số</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2" >
                            <select class="form-select donvitinhchitiet" aria-label="Default select example" >
                                <option selected="">Chọn đơn vị tính</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-2">
                            <button type="submit" id="timkiemVatTuChiTiet" class="btn btn-primary" style="margin-left: -5px">Tìm kiếm</button>
                        </div>
                    </div>
                </div>
                <div style="display: flex; align-items: center;">
                    <h6 class="modal-title" id="orderTitle">Đơn hàng:</h6>
                    <span style="margin-left: 8px;">|</span>
                    <h6 style="margin-left: 8px;" class="modal-title" id="tongsovattu">Tổng vật tư:</h6>
                    <span style="margin-left: 8px;">|</span>
                    <h6 style="margin-left: 8px;" class="modal-title" id="tongdanhan">Tổng đã nhận:</h6>
                    <span style="margin-left: 8px;">|</span>
                    <h6 style="margin-left: 8px;" class="modal-title" id="tongchuanhan">Tổng chưa nhận:</h6>
                    <span style="margin-left: 8px;">|</span>
                    <h6 style="margin-left: 8px;" class="modal-title" id="tongdaxuat">Tổng đã xuất:</h6>
                </div>
                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-bordered table-hover danhmucvattuchitiet">
                        <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">STT</th>
                            <th style="text-align: center; vertical-align: middle;">Tên vật tư</th>
                            <th style="text-align: center; vertical-align: middle;">Mã số</th>
                            <th style="text-align: center; vertical-align: middle;">Đơn vị tính</th>
                            <th style="text-align: center; vertical-align: middle;">Số lượng nhận</th>
                            <th style="text-align: center; vertical-align: middle;">Thực nhận</th>
                            <th style="text-align: center; vertical-align: middle;">ghi chú</th>
                            <th style="text-align: center; vertical-align: middle;">Mã vật tư trên đơn hàng</th>
                            <th style="text-align: center; vertical-align: middle;">Số đơn hàng/hợp đồng</th>

                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">trở lại</button>
                </div>
            </div>
        </div>
        </div>
    </div>
@endsection
@section('script')
<script src="{{asset('assets/js/select2.min.js')}}"></script>
<!-- QUẢN LÝ THEO ĐƠN HÀNG -->
    {{-- LẤY PHÂN KHÚC --}}
        <script>
            $(document).ready(function(){
                $('.thuongHieuDonHang').change(function(){
                    var phanKhucSelect = $('.phanKhucDonHang'); // Chọn select box thứ hai
                    var duanSelect = $('.duAnDonHang'); // Chọn select box thứ hai
                    var donhangSelect = $('.donHangChiTiet'); // Chọn select box thứ hai
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
                $('.phanKhucDonHang').change(function(){
                    var segmentId = $(this).val();
                    var projectSelect = $('.duAnDonHang');
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
                $('.donHangChiTiet').select2({
                    placeholder: "Chọn...",
                    allowClear: true,
                    width: '100%'
                });

                $('.duAnDonHang').change(function(){
                    var projectId = $(this).val();
                    var orderSelect = $('.donHangChiTiet');
                    orderSelect.empty().append('<option></option>');

                    if(projectId != ''){
                        $.ajax({
                            url: "{{ route('selectedDonHang') }}",
                            method: 'GET',
                            data: {projectId: projectId},
                            success: function(data){
                                if(data.orders && data.orders.length > 0){
                                    $.each(data.orders, function(index, order){
                                        var newOption = new Option(order.sodonhang, order.id, false, false);
                                        orderSelect.append(newOption);
                                    });

                                    orderSelect.prop('disabled', false).select2({
                                        placeholder: "Chọn đơn hàng",
                                        allowClear: true,
                                        width: '100%'
                                    });
                                } else {
                                    orderSelect.empty().prop('disabled', true).select2({
                                        placeholder: "Không có dữ liệu",
                                        allowClear: true,
                                        width: '100%'
                                    });
                                }
                            }
                        });
                    } else {
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
                $(document).ready(function() {
                    $('#timkiemTonKhoDonHang').click(function(event) {
                        event.preventDefault(); // Prevent the form from submitting via the browser.

                        var thuongHieuIdDonHang = $('.thuongHieuDonHang').prop('disabled') ? '' : $('.thuongHieuDonHang').val();
                        var phanKhucIdDonHang = $('.phanKhucDonHang').prop('disabled') ? '' : $('.phanKhucDonHang').val();
                        var duAnIdDonHang = $('.duAnDonHang').prop('disabled') ? '' : $('.duAnDonHang').val();
                        var donHangIdChiTiet = $('.donHangChiTiet').prop('disabled') ? '' : $('.donHangChiTiet').val();

                        $.ajax({
                            url: "{{ route('listTonKhoDonHang') }}",
                            type: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                thuongHieuIdDonHang: thuongHieuIdDonHang,
                                phanKhucIdDonHang: phanKhucIdDonHang,
                                duAnIdDonHang: duAnIdDonHang,
                                donHangIdChiTiet: donHangIdChiTiet,
                            },
                            success: function(response) {
                                var tableBody = $("#bangTonKhoTheoDonHang tbody");
                                tableBody.empty();

                                if (response && response.length > 0) {
                                    $('#labelThuongHieuDonHang').text('Thương Hiệu: ' + (response[0].brand || 'N/A'));
                                    $('#labelPhanKhucDonHang').text('Phân khúc: ' + (response[0].segment || 'N/A'));
                                    $('#labelDuAnDonHang').text('Dự án: ' + (response[0].project || 'N/A'));
                                    $.each(response, function(index, order) {
                                        console.log(order);
                                        var stt = index + 1;
                                        var row = `<tr data-id="${order.id}">
                                            <td style="text-align: center">${stt}</td>
                                            <td style="text-align: center">${order.sodonhang}</td>
                                            <td style="text-align: center">${order.tongSoLuongVatTu}</td> <!-- Display the total quantity of supplies -->
                                            <td style="text-align: center">${order.tongSoLuongNhan}</td>
                                            <td style="text-align: center">${order.tongSoLuongXuat}</td>
                                            <td style="text-align: center">${order.soluongTon}</td>

                                        </tr>`;
                                        tableBody.append(row);
                                    });
                                } else {
                                    tableBody.append('<tr><td colspan="7" style="text-align: center">Không có dữ liệu</td></tr>');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error(error);
                                alert('Lỗi khi tải dữ liệu: ' + error);
                            }
                        });
                    });
                });
        </script>



    {{-- LẤY THÔNG TIN VẬT TƯ CHI TIẾT --}}
        <script>
            $(document).ready(function() {
                    $('#bangTonKhoTheoDonHang tbody').on('click', 'tr', function(event) {
                        if ($(event.target).closest('.no-modal-trigger').length) {
                            // Ngăn chặn hiển thị modal khi click vào vùng không mong muốn
                            return;
                        }

                        var orderId = $(this).data('id'); // Giả sử mỗi hàng trong bảng có attribute 'data-id' chứa ID của đơn hàng

                        // Thực hiện AJAX call để lấy dữ liệu chi tiết vật tư
                        $.ajax({
                            url: "{{ route('DuLieuVatTuChiTiet') }}", // Sửa lại URL theo route của bạn
                            type: 'POST',
                            data: {
                                id: orderId,
                                _token: '{{ csrf_token() }}' // CSRF token của Laravel
                            },
                            success: function(data) {
                                $('#timkiemVatTuChiTiet').attr('data-id', orderId);
                                var selectTenVatTu = $('.tenvattuchitiet').empty().append('<option selected="">Tên vật tư</option>');
                                var selectMaSo = $('.masochitiet').empty().append('<option selected="">Mã số</option>');
                                var selectDonViTinhChiTiet = $('.donvitinhchitiet').empty().append('<option selected="">Đơn vị Tính</option>');

                                var donvitinhSet = new Set();
                                $.each(data.supplies, function(index, item) {
                                    selectTenVatTu.append(new Option(item.tenvattu, item.tenvattu));
                                    selectMaSo.append(new Option(item.maso, item.maso));
                                    if (!donvitinhSet.has(item.donvitinh)) {
                                        selectDonViTinhChiTiet.append(new Option(item.donvitinh, item.donvitinh));
                                        donvitinhSet.add(item.donvitinh);
                                    }
                                });
                                var tbody = $('#danhMucVatTuChiTiet').find('.danhmucvattuchitiet tbody');
                                tbody.empty();
                                // Cập nhật các thông tin chi tiết về đơn hàng vào modal
                                $('#orderTitle').text('Đơn hàng: ' + data.orderName);
                                $('#tongsovattu').text('Tổng vật tư: ' + data.totalSupplies);
                                $('#tongdanhan').text('Tổng đã nhận: ' + data.totalDanhan);
                                $('#tongchuanhan').text('Tổng chưa nhận: ' + data.totalChuanhan);

                                // Thêm dữ liệu vật tư vào bảng trong modal
                                $.each(data.supplies, function(index, item) {
                                    console.log(item)
                                    var row = $('<tr>').append(
                                        $('<td style="text-align: center; vertical-align: middle;">').text(index + 1),
                                        $('<td style="text-align: center; vertical-align: middle;">').text(item.tenvattu),
                                        $('<td style="text-align: center; vertical-align: middle;">').text(item.maso),
                                        $('<td style="text-align: center; vertical-align: middle;">').text(item.donvitinh),
                                        $('<td style="text-align: center; vertical-align: middle;">').text(item.soluong),
                                        $('<td style="text-align: center; vertical-align: middle;">').text(item.danhan),
                                        $('<td style="text-align: center; vertical-align: middle;">').text(item.ghichu),
                                        $('<td style="text-align: center; vertical-align: middle;">').text('-'),
                                        $('<td style="text-align: center; vertical-align: middle;">').text('-')
                                    );
                                    tbody.append(row);
                                });

                                // Hiển thị modal sau khi đã cập nhật xong dữ liệu
                                $('#danhMucVatTuChiTiet').modal('show');
                            },
                            error: function(error) {
                                console.log(error);
                                alert('Có lỗi xảy ra');
                            }
                        });
                    });

                    // Cấu hình select2 cho các trường select trong modal sau khi modal được hiển thị
                    $('#danhMucVatTuChiTiet').on('shown.bs.modal', function() {
                        $('.tenvattuchitiet, .masochitiet, .donvitinhchitiet').select2({
                            placeholder: "Chọn...",
                            allowClear: true,
                            width: '100%',
                            dropdownParent: $('#danhMucVatTuChiTiet')
                        });
                    });
                });


        </script>
    {{-- TÌM KIẾM TÊN VẬT TƯ CHI TIẾT --}}
        <script>
            $(document).ready(function(){
                $("#timkiemVatTuChiTiet").click(function() {
                    var tenvattu = $(".tenvattuchitiet").val() === "Tên vật tư" ? "" : $(".tenvattuchitiet").val();
                    var maso = $(".masochitiet").val() === "Mã số" ? "" : $(".masochitiet").val();
                    var donvitinh = $(".donvitinhchitiet").val() === "Đơn vị Tính" ? "" : $(".donvitinhchitiet").val()
                    var orderId = $(this).attr('data-id'); // Đảm bảo đã lưu orderId vào attribute 'data-id' của nút tìm kiếm
                    $.ajax({
                        url: "{{ route('timkiemvattuchitiet') }}", // Sửa lại URL cho đúng với route của bạn
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: '{{ csrf_token() }}', // CSRF token
                            tenvattu: tenvattu,
                            maso: maso,
                            donvitinh: donvitinh,
                            orderId: orderId,
                        },
                        success: function(data) {
                            // Xử lý dữ liệu trả về và hiển thị trong bảng
                            var tbody = $("#danhMucVatTuChiTiet .table-responsive tbody");
                            tbody.empty(); // Xóa nội dung hiện tại của tbody
                            if(data.supplies && data.supplies.length > 0){
                                $.each(data.supplies, function(index, supply) {
                                    tbody.append(
                                        `<tr>
                                            <td style="text-align:center;vertical-align: middle">${index + 1}</td>
                                            <td style="text-align:center;vertical-align: middle">${supply.tenvattu}</td>
                                            <td style="text-align:center;vertical-align: middle">${supply.maso}</td>
                                            <td style="text-align:center;vertical-align: middle">${supply.donvitinh}</td>
                                            <td style="text-align:center;vertical-align: middle">${supply.soluong}</td>
                                            <td style="text-align:center;vertical-align: middle">${supply.danhan}</td>
                                            <td style="text-align:center;vertical-align: middle">-</td>
                                            <td style="text-align:center;vertical-align: middle">-</td>
                                            <td style="text-align:center;vertical-align: middle">-</td>
                                        </tr>`
                                    );
                                });
                            } else {
                                tbody.append(
                                    `<tr>
                                        <td colspan="9" style="text-align:center;vertical-align: middle">Không có dữ liệu</td>
                                    </tr>`
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            alert("Có lỗi xảy ra: " + error);
                        }
                    });
                });
            });
        </script>

@endsection
