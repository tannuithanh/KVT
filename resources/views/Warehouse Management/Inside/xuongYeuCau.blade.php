@extends('Layout.app')
@section('style')
    <style>
        .centered {
            text-align: center;
        }
        .search-and-results {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin-top: 20px;
            /* Tạo khoảng cách giữa các phần khác nếu cần */
        }

        .search-select {
            width: 100%;
            margin-bottom: 10px;
            /* Tạo khoảng cách giữa các phần tử select */
        }

        .table-responsive {
            margin-top: 20px;
        }

        .cart-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 5px;  /* Giảm padding */
            margin-bottom: 5px; /* Giảm margin dưới */
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .cart-card-body {
            display: flex;
            width: 100%;
            justify-content: space-between;
            align-items: center;
        }

        .cart-info {
            display: flex;
            flex: 1; /* Đảm bảo phần này mở rộng tối đa */
            align-items: center;
            gap: 10px; /* Giảm khoảng cách giữa các thông tin */
        }

        .card-title {
            font-size: 13px; /* Điều chỉnh cho nhỏ lại */
            font-weight: bold;
            overflow: hidden; /* Ngăn chặn tràn nội dung */
        }

        .card-text {
            font-size: 13px; /* Đồng bộ kích thước với tên vật tư */
            color: #666;
            overflow: hidden; /* Ngăn chặn tràn nội dung */
        }

        .cart-controls {
            display: flex;
            gap: 5px; /* Giảm khoảng cách giữa các điều khiển */
        }

        .qty-input {
            width: 50px; /* Điều chỉnh chiều rộng input */
            padding: 5px 10px; /* Điều chỉnh đệm */
        }

        .btn {
            padding: 5px 10px; /* Điều chỉnh đệm cho nút */
        }


    </style>

    <link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('title')
    Yêu cầu xuất vật tư
@endsection

@section('content')
    <div class="pagetitle">
        <h1>Yêu cầu xuất vật tư</h1>
    </div>
    <section class="section">
                <div class="row">
                    <div class="col-lg-3">
                        <div class="card" style="height: 350px">
                            <div class="card-body">
                                <h5 class="mt-3">Tìm kiếm vật tư</h5>
                                <select class="form-control search-select thuongHieu">
                                    <option value="" selected>Chọn thương hiệu</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>

                                <select class="form-control search-select phanKhuc" disabled>
                                    <option value="" selected>Chọn phân khúc</option>
                                    <!-- Các phân khúc khác -->
                                </select>
                                <select class="form-control search-select duAn" disabled>
                                    <option selected>Chọn dự án</option>
                                    <!-- Các dự án khác -->
                                </select>
                                <select class="form-control search-select tenVatTu" disabled>
                                    <option selected>Tìm kiếm vật tư</option>
                                    <!-- Các dự án khác -->
                                </select>
                                <button type="button" class="btn btn-primary mt-3 timKiemVatTu">Tìm kiếm</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="card" style="height: 350px">
                            <div class="card-body">
                                <h5 class="mt-3">Bảng Vật Tư</h5>
                                <div class="table-responsive" style="height: 220px">
                                        <span style="display: inline-block;" ><strong id="resultThuongHieu">Thương hiệu:</strong></span>|
                                        <span style="display: inline-block;" ><strong id="resultPhanKhuc">Phân khúc:</strong></span>|
                                        <span style="display: inline-block;" ><strong id="resultDuAn">Dự án:</strong></span>
                                    <table class="table table-bordered vattuChiTiet">
                                        <thead>
                                            <tr>
                                                <th>STT</th>
                                                <th>Tên Vật Tư</th>
                                                <th>Mã Số</th>
                                                <th>Tồn Kho</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Dữ liệu bảng ở đây -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card" style="height: 350px">
                            <div class="card-body">
                                <h5 class="mt-3">
                                    Giỏ hàng: <span id="itemCount" style="color: red">0</span> món
                                </h5>
                                <div id="cartItems" class="cart-items" style="height: 200px; overflow-y: auto;">
                                    <!-- Các thẻ card cho mỗi mặt hàng sẽ được thêm vào đây -->
                                </div>
                                <button type="button" class="btn btn-outline-primary mt-3" id="taoPhieu" style="display:none;">Tạo phiếu</button>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mt-3">Thông tin phiếu đề nghị cấp vật tư</h5>
                            </div>
                        </div>
                    </div>
                </div>

    </section>
@endsection
{{-- CARD IN TRÌNH KÝ --}}
<div id="printCard" class="card" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center; z-index: 1050; padding: 40px;">
    <div class="card-body" style="width: 250mm; height: 210mm; background-color: white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px; margin: auto; font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; overflow: auto; display: flex; flex-direction: column;">
            <div class="table-responsive">
                <table class="table table-bordered" >
                    <thead>
                        <tr>
                            <th style="text-align: left; vertical-align: middle; width: 20%; background-color: white !important; color: black !important">
                                <img src="{{ asset('assets/img/logo.png') }}" alt="logo" style="height: 25px;">
                            </th>
                            <th id="soPhieu" style="text-align: center; vertical-align: middle; font-size: 20px; background-color: white !important; color: black !important">
                                PHIẾU ĐỀ NGHỊ CẤP VẬT TƯ <br>
                                Số :………
                            </th>
                            <!-- Cột mã biểu mẫu -->
                            <th style="text-align: center; vertical-align: middle; font-size: 14px; width: 20%; background-color: white !important; color: black !important">
                                QT.VPCL.TM 01-BM06
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div style="padding-left: 20px;">
                <p style="margin: 0; font-size: 16px;"><strong>Đơn vị yêu cầu:</strong> Xưởng Thân vỏ & khung gầm</p>
                <p style="margin: 0; font-size: 16px;"><strong>Mục đích cấp:</strong> Vật tư phục vụ gia công cải tạo khung xương sàn chính</p>
                <p style="margin: 0; font-size: 16px;" id="thoiGianCap"><strong>Thời gian cấp:</strong> …</p>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="bangVatTuDeNghi" style="width: 100%; margin-top: 10px;">
                    <thead>
                        <tr>
                            <th style="text-align: center; vertical-align: middle;">Stt</th>
                            <th style="text-align: center; vertical-align: middle;">Mã số</th>
                            <th style="text-align: center; vertical-align: middle;">Tên vật tư</th>
                            <th style="text-align: center; vertical-align: middle;">Đvt</th>
                            <th style="text-align: center; vertical-align: middle;">Số lượng</th>
                            <th style="text-align: center; vertical-align: middle;">Thương hiệu</th>
                            <th style="text-align: center; vertical-align: middle;">Đơn hàng/PYC</th>
                            <th style="text-align: center; vertical-align: middle;">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div style="text-align: right;" id="ngayThangNam">
                Núi Thành, Ngày ... Tháng ... Năm ...
            </div>

            <div style="display: flex; justify-content: space-between; ">
                <div style="text-align: center;">
                    <p><strong>PGĐ. SXM</strong></p>
                    <p>Lê Văn Doanh</p>
                </div>
                <div style="text-align: center;">
                    <p><strong>Kiểm tra</strong></p>
                    <p>....</p>
                </div>
                <div style="text-align: center;">
                    <p><strong>Người lập</strong></p>
                    <p>....</p>
                </div>
            </div>

            <div style="text-align: center; margin-top: auto;">
                <hr>
                <button type="button" class="btn btn-primary" id="luuPhieu">Lưu</button>
                <button type="button" class="btn btn-secondary">Trở về</button>
            </div>
    </div>
</div>
@section('script')
<script src="{{asset('assets/js/select2.min.js')}}"></script>
{{-- TÌM KIẾM PHÂN KHÚC --}}
    <script>
        $(document).ready(function() {
            $('.thuongHieu').on('change', function() {
                var selected = $(this).val(); // Lấy giá trị được chọn
                if (selected === '') { // Nếu chọn giá trị mặc định "Chọn thương hiệu"
                    // Disable tất cả các select khác
                    $('.phanKhuc').prop('disabled', true);
                    $('.duAn').prop('disabled', true);
                    $('.tenVatTu').prop('disabled', true);
                } else {
                    // Gửi yêu cầu AJAX để lấy dữ liệu phân khúc như đã mô tả trước đó
                    $.ajax({
                        url: "{{ route('slectedPhanKhuc') }}",
                        type: 'GET',
                        data: { brand_id: selected },
                        success: function(data) {
                            // Cập nhật và enable select phân khúc
                            var phanKhucSelect = $('.phanKhuc');
                            phanKhucSelect.empty().append('<option value="" selected>Chọn phân khúc</option>');
                            $.each(data, function(key, segment) {
                                phanKhucSelect.append(`<option value="${segment.id}">${segment.name}</option>`);
                            });
                            phanKhucSelect.prop('disabled', false);

                            // Tiếp tục các bước tương tự cho dự án và vật tư nếu cần
                        },
                        error: function(error) {
                            console.error('Error:', error);
                        }
                    });
                }
            });
        });
    </script>
{{-- TÌM KIẾM DỰ ÁN --}}
    <script>
        $(document).ready(function() {
            $('.phanKhuc').on('change', function() {
                var segmentId = $(this).val();
                // Nếu một phân khúc được chọn
                if (segmentId) {
                    $.ajax({
                        url: "{{ route('slectedDuAn') }}",
                        type: 'GET',
                        data: { segmentId: segmentId },
                        success: function(data) {
                            var duAnSelect = $('.duAn');
                            duAnSelect.empty();
                            duAnSelect.append('<option value="">Chọn dự án</option>'); // Sửa chỗ này
                            $.each(data, function(key, project) {
                                duAnSelect.append(`<option value="${project.id}">${project.name}</option>`);
                            });
                            duAnSelect.prop('disabled', false);
                        },
                    });
                } else {
                    // Nếu không có phân khúc được chọn, disable select dự án và vật tư
                    $('.duAn').prop('disabled', true);
                    $('.tenVatTu').prop('disabled', true);
                }
            });
        });
    </script>
{{-- TÌM KIẾM VẬT TƯ THEO DỰ ÁN --}}
    <script>
        $(document).ready(function() {
            $('.duAn').on('change', function() {
                var projectId = $(this).val();
                if (projectId) {
                    $.ajax({
                        url: "{{ route('layVatTutheoDuAn') }}",
                        type: 'GET',
                        data: { projectId: projectId },
                        success: function(data) {

                            var tenVatTuSelect = $('.tenVatTu');
                            tenVatTuSelect.empty();
                            tenVatTuSelect.append('<option value="">Chọn vật tư</option>');
                            $.each(data, function(i, vatTu) {
                                tenVatTuSelect.append(`<option value="${vatTu.id}">${vatTu.maso} - ${vatTu.name}</option>`);
                            });
                            // Khởi tạo Select2
                            tenVatTuSelect.select2({
                                placeholder: "Chọn vật tư",
                                allowClear: true
                            });
                            tenVatTuSelect.prop('disabled', false);
                        },
                        error: function() {
                            alert('Lỗi khi tải dữ liệu vật tư!');
                        }
                    });
                } else {
                    $('.tenVatTu').prop('disabled', true);
                }
            });
        });
    </script>
{{-- HIỂN THỊ VẬT TƯ SANG BẢNG --}}
    <script>
        $(document).ready(function() {
            $('.timKiemVatTu').on('click', function() {
                var thuongHieu = $('.thuongHieu').val();
                var tenThuongHieu = $('.thuongHieu option:selected').text();
                $('#resultThuongHieu').text('Thương hiệu: ' + (tenThuongHieu !== 'Chọn thương hiệu' ? tenThuongHieu : 'Không xác định'));

                // Lấy giá trị từ thẻ select phân khúc
                var phanKhuc = $('.phanKhuc').val();
                var tenPhanKhuc = $('.phanKhuc option:selected').text();
                $('#resultPhanKhuc').text('Phân khúc: ' + (tenPhanKhuc !== 'Chọn phân khúc' ? tenPhanKhuc : 'Không xác định'));
                var duAn = $('.duAn').val();
                var tenDuAn = $('.duAn option:selected').text();
                $('#resultDuAn').text('Dự án: ' + (tenDuAn !== 'Chọn dự án' ? tenDuAn : 'Không xác định'));
                // Lấy giá trị từ các select
                var thuongHieu = $('.thuongHieu').val();
                var phanKhuc = $('.phanKhuc').val();
                var duAn = $('.duAn').val();
                var idVatTu = $('.tenVatTu').val(); // Có thể được chọn hoặc không

                // Kiểm tra các điều kiện cần thiết
                if (!thuongHieu) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Thiếu thông tin!',
                        text: 'Vui lòng chọn thương hiệu',
                        confirmButtonText: 'Đóng'
                    });
                } else if (!phanKhuc) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Thiếu thông tin!',
                        text: 'Vui lòng chọn phân khúc',
                        confirmButtonText: 'Đóng'
                    });
                } else if (!duAn) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Thiếu thông tin!',
                        text: 'Vui lòng chọn dự án',
                        confirmButtonText: 'Đóng'
                    });
                } else {
                    // Nếu các thông tin cần thiết đã được chọn, gửi dữ liệu qua AJAX
                    $.ajax({
                        url: '{{ route("layVatTuHienThi") }}',
                        type: 'POST',
                        data: {
                            thuongHieu: thuongHieu,
                            phanKhuc: phanKhuc,
                            duAn: duAn,
                            idVatTu: idVatTu, // Gửi dữ liệu này ngay cả khi nó có thể không được chọn
                            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                        },
                        success: function(response) {
                            console.log(response)
                            var tbody = $('.vattuChiTiet tbody');
                            tbody.empty(); // Xóa các hàng hiện tại để thêm mới

                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(supply, index) {
                                    var row = `<tr data-tendonhang="${supply.order.sodonhang}" data-dvt="${supply.donvitinh}">
                                                <td class="centered">${index + 1}</td>
                                                <td class="centered">${supply.tenvattu}</td>
                                                <td class="centered">${supply.maso}</td>
                                                <td class="centered">${supply.soluong_conlai}</td>
                                                <td class="centered"><button class="bx bxs-cart-add btn btn-outline-primary addGioHang" title="Thêm vào giỏ hàng"></button></td>
                                            </tr>`;
                                    tbody.append(row);
                                });
                            }else {
                                // Trường hợp không có dữ liệu, hiển thị thông báo không tìm thấy vật tư
                                tbody.append('<tr><td colspan="5" class="text-center">Không có dữ liệu</td></tr>');
                            }
                        },
                        error: function(error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: 'Có lỗi xảy ra trong quá trình tìm kiếm.',
                                confirmButtonText: 'Đóng'
                            });
                        }
                    });
                }
            });
        });


    </script>
{{-- HIỂN THỊ TỪ BẢNG SANG GIỎ HÀNG --}}
    <script>
        var addedItems = {};

        function updateItemCount() {
            var count = $('#cartItems .cart-card').length;  // Đếm số lượng card trong giỏ hàng
            $('#itemCount').text(count);  // Cập nhật số lượng hiển thị
            if (count > 0) {
                $('#taoPhieu').show();  // Hiển thị nút nếu có ít nhất một mặt hàng
            } else {
                $('#taoPhieu').hide();  // Ẩn nút nếu không có mặt hàng nào
            }
        }

        $(document).ready(function() {
            $('body').on('click', '.addGioHang', function() {
                var row = $(this).closest('tr');
                var maso = row.find('td:eq(2)').text();
                var tenVatTu = row.find('td:eq(1)').text();
                var soluongConLai = row.find('td:eq(3)').text();
                var donViTinh = row.data('dvt');
                if (!addedItems[maso]) {
                    addedItems[maso] = true;
                    var cardHtml = `<div class="cart-card" data-dvt="${donViTinh}">
                        <div class="cart-card-body">
                            <div class="cart-info">
                                <div class="card-title" >${tenVatTu}</div>
                                <div class="card-text">Mã số: ${maso}</div>
                            </div>
                            <div class="cart-controls">
                                <input type="number" class="form-control qty-input" value="1" min="1" max="${soluongConLai}">
                                <button class="btn btn-danger removeItem">Xóa</button>
                            </div>
                        </div>
                    </div>`;
                    $('#cartItems').append(cardHtml);
                    $(this).prop('disabled', true);
                    updateItemCount();  // Cập nhật số lượng món hàng sau khi thêm
                }
            });

            $('body').on('click', '.removeItem', function() {
                var card = $(this).closest('.cart-card');
                var maso = card.find('.card-text').text().split(': ')[1];
                addedItems[maso] = false;
                card.remove();
                $('.addGioHang').each(function() {
                    if ($(this).closest('tr').find('td:eq(2)').text() === maso) {
                        $(this).prop('disabled', false);
                    }
                });
                updateItemCount();  // Cập nhật số lượng món hàng sau khi xóa
            });
        });
    </script>
{{-- TẠO PHIẾU --}}
    <script>
        $(document).ready(function() {
            $('#taoPhieu').click(function() {
                $('#soPhieu').html('PHIẾU ĐỀ NGHỊ CẤP VẬT TƯ <br> Số: <input type="text" placeholder="Nhập số phiếu">');
                $('#thoiGianCap').html('Thời gian cấp: <input type="date">');
                $('#ngayThangNam').html('Núi Thành, <input type="date">');
                $(this).hide();
                $('#printCard').show();
                var stt = 1;
                $('#cartItems .cart-card').each(function() {
                    var maso = $(this).find('.cart-info .card-text').text().split(':')[1].trim(); // Mã số
                    var tenvattu = $(this).find('.cart-info .card-title').text(); // Tên vật tư
                    var soluong = $(this).find('.cart-controls .qty-input').val(); // Số lượng
                    var thuongHieu = $('#resultThuongHieu').text().replace('Thương hiệu: ', '').trim();
                    var duAn = $('#resultDuAn').text().replace('Dự án: ', '').trim();
                    var donViTinh = $(this).data('dvt');
                    var soDonHang = $('.vattuChiTiet tbody tr').first().data('tendonhang');
                    var newRow = `<tr>
                        <td style="text-align: center;">${stt++}</td>
                        <td style="text-align: center;">${maso}</td>
                        <td style="text-align: center;">${tenvattu}</td>
                        <td style="text-align: center;">${donViTinh}</td>
                        <td style="text-align: center;">${soluong}</td>
                        <td style="text-align: center;">${thuongHieu}</td>
                        <td style="text-align: center;">${soDonHang}</td>
                        <td style="text-align: center;">${duAn}</td>
                    </tr>`;

                    $('#bangVatTuDeNghi tbody').append(newRow);
                });
            });
        });
    </script>
{{-- LƯU PHIẾU --}}

@endsection

