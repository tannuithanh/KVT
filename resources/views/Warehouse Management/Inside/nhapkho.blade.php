@extends('Layout.app')
@section('style')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/choices.min.css')}}">
<style>
    .blink-warning {
            background-color: rgba(255, 255, 0, 0.329) !important
        }
    .table-hover tbody tr:hover {
        cursor: pointer;
    }
    .fixed-barcode-scanner {
            position: sticky;
            top: 10px;
            z-index: 9999;
            height: 400px;
            width: 100%;
            border: 3px solid #ddd;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .modal-content {
            border-radius: 10px;
        }
        .btn-close {
            background: none;
            border: none;
        }
    #barcode-scanner {
            display: none;
            position: relative;
            z-index: 1050;
        }
</style>
<style>
    #selectedItemsTable th, #selectedItemsTable td {
        text-align: center;
        vertical-align: middle;
    }
    #selectedItemsTable th:nth-child(4), #selectedItemsTable td:nth-child(4) {
        width: 100px;
    }
    .soluongin {
        width: 80px; /* Điều chỉnh độ rộng của input */
        text-align: center;
    }
</style>
@endsection
@section('title')
    Nhập kho
@endsection
@section('content')
<div class="pagetitle">
    <h1>Nhập kho</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Trang chủ</a></li>
            <li class="breadcrumb-item">Nhập kho</li>
        </ol>
    </nav>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center mt-2">
                        <h6 class="modal-title" id="orderTitle">Đơn hàng:</h6>
                        <span>|</span>
                        <h6 class="modal-title" id="tongsovattu">Tổng vật tư:</h6>
                        <span>|</span>
                        <h6 class="modal-title" id="tongdanhan">Tổng đã nhận:</h6>
                        <span>|</span>
                        <h6 class="modal-title" id="tongchuanhan">Tổng chưa nhận:</h6>
                        <span>|</span>
                        <h6 class="modal-title" id="tongdaxuat">Tổng đã xuất:</h6>
                    </div>
                    <div class="table-responsive mt-3">
                        <button id="chonVatTu" type="button" class="btn btn-outline-success mt-2"></i>Chọn vật tư in</button>
                        <button id="nhapKho" type="button" class="btn btn-outline-primary mt-2"><i class="bi bi-box-arrow-in-down"></i> Nhập kho</button>

                        <table class="table table-borderless table-bordered table-hover mt-2 vatTuDonHang">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">STT</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Tên vật tư</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Mã số</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Mã số mới</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Đơn vị tính</th>
                                    <th colspan="5" style="text-align: center;">Tình trạng</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Số lượng in</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">ghi chú</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;">Tổng</th>
                                    <th style="text-align: center; vertical-align: middle;">Đã nhận</th>
                                    <th style="text-align: center; vertical-align: middle;">Lưu kho</th>
                                    <th style="text-align: center; vertical-align: middle;">Chưa nhận</th>
                                    <th style="text-align: center; vertical-align: middle;">Đã xuất</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <button id="inVatTu" type="button" style="display: none" class="btn btn-outline-success mt-2"><i class="bi bi-printer"></i>In mã barcode</button>
                        <button id="nhanVatTu" type="button" class="btn btn-outline-primary mt-2" style="display: none"><i class="bi bi-box-arrow-in-down"></i> Nhận vật tư</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- HIỂN THỊ POPUP --}}
    <div class="modal fade" id="actionModal" tabindex="-1" aria-labelledby="actionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="actionModalLabel">Chọn Hành Động</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body POPUPCHON">
                    <div id="barcode-scanner" class="fixed-barcode-scanner">
                        <video id="video" style="width: 100%; height: 100%; object-fit: contain;" autoplay></video>
                    </div>
                    <button type="button" class="btn btn-primary w-100 mb-3" id="NTDH" onclick="showOrderNameModal()">
                        <i class="bi bi-pencil-square"></i> Nhập tên đơn hàng
                    </button>
                    <button type="button" class="btn btn-secondary w-100" id="scan-button">
                        <i class="ri-qr-code-line"></i> Quét QR
                    </button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

{{-- LỊCH SỬ --}}
    <div class="modal fade" id="historyTransaction" style="background-color: #000000bb" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lịch sử giao dịch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ">
                    <div class="table-responsive" style="max-height: 450px;">
                        <table class="table table-bordered vatTuDonHang">
                            <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">STT</th>
                                <th style="text-align: center; vertical-align: middle;">Tên vật tư</th>
                                <th style="text-align: center; vertical-align: middle;">Mã số</th>
                                <th style="text-align: center; vertical-align: middle;">Loại giao dịch</th>
                                <th style="text-align: center; vertical-align: middle;">Số lượng</th>
                                <th style="text-align: center;">Ngày giao dịch</th>
                                <th style="text-align: center; vertical-align: middle;">Ghi chú</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- XÁC NHẬN VẬT TƯ INBARCODE --}}
    <div class="modal fade" id="basicModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thông tin in</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered" id="selectedItemsTable">
                            <thead>
                                <tr>
                                    <th>Stt</th>
                                    <th>Tên Vật Tư</th>
                                    <th>Mã Số</th>
                                    <th style="width: 100px;">Số Lượng In</th>
                                    <th>Mã Barcode</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dữ liệu sẽ được thêm vào đây -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
                    <button type="button" class="btn btn-primary" id="printerBarcode">In mã</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
<script src="{{asset('assets/js/choices.min.js')}}"></script>
<script src="{{asset('assets/js/html5-qrcode.min.js')}}"></script>
{{-- CHUYỂN ĐỔI THÀNH ĐƠN HÀNG SELECT2 --}}
    <script type="text/javascript">
        $(document).ready(function() {
            var orders = @json($orders);
            $('#actionModal').modal('show');

            // Hàm để hiển thị modal và cập nhật nội dung của nó
            window.showOrderNameModal = function() {
                $('#actionModalLabel').text('Nhập Tên Đơn Hàng');
                var selectHTML = '<label for="orderSelect">Chọn đơn hàng:</label>' +
                    '<select id="orderSelect" class="form-control">' +
                    '<option value="">Chọn một đơn hàng...</option>';

                // Thêm các đơn hàng vào thẻ select
                orders.forEach(function(order) {
                    selectHTML += `<option value="${order.id}">${order.sodonhang}</option>`;
                });
                selectHTML += '</select>' +
                    '<button onclick="searchOrder()" class="btn btn-primary w-100 mt-3">Tìm kiếm</button>';

                $('#actionModal .modal-body').html(selectHTML);

                // Khởi tạo Choices
                var element = document.getElementById('orderSelect');
                var choices = new Choices(element, {
                    searchEnabled: true,
                    itemSelectText: '',
                    removeItemButton: true,
                });
            };

            // Hàm để xử lý sự kiện tìm kiếm đơn hàng
            window.searchOrder = function() {
                var orderId = $('#orderSelect').val();
                console.log('Đơn hàng được chọn:', orderId);

                if (orderId) {
                    const $actionModal = $('#actionModal');
                    const $vatTuDonHangTableBody = $('.vatTuDonHang tbody');
                    const $orderTitle = $('#orderTitle');
                    const $tongSoVatTu = $('#tongsovattu');
                    const $tongDaNhan = $('#tongdanhan');
                    const $tongChuaNhan = $('#tongchuanhan');
                    const $tongDaXuat = $('#tongdaxuat');
                    $actionModal.modal('hide');
                    $actionModal.remove();
                    $.ajax({
                        url: "{{ route('layThongTinDonHang') }}",
                        type: 'POST',
                        data: {
                            id: orderId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log(response)
                            $vatTuDonHangTableBody.empty();
                            response.suppliesDetail.forEach((supplyDetail, index) => {
                                var totalNhapKho = supplyDetail.viewVatTuChiTietData.reduce((sum, vtct) => sum + vtct.soluongnhapkho, 0);
                                var totalDatChatLuong = supplyDetail.viewVatTuChiTietData.reduce((sum, vtct) => sum + vtct.soluongdatchatluong, 0);

                                // Kiểm tra điều kiện để thêm class blink-warning
                                var rowClass = totalNhapKho > totalDatChatLuong ? 'blink-warning' : '';

                                const row = `
                                    <tr id="supply-row-${supplyDetail.supply.id}" data-id="${supplyDetail.supply.id}">
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} stt">${index + 1}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} tenvattu">${supplyDetail.supply.tenvattu}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} maso">${supplyDetail.supply.maso}</td>
                                         <td style="text-align: center; vertical-align: middle" class="${rowClass} maso">${supplyDetail.supply.maso_new ?? ""}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} donvitinh">${supplyDetail.supply.donvitinh}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} soluong">${supplyDetail.supply.soluong}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} soluongnhapkho">${totalNhapKho}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} soluongdatchatluong">${totalDatChatLuong}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} chuanhan">${supplyDetail.chuanhan}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} daxuat">${supplyDetail.daxuat}</td>
                                        <td style="display:none" class="barcode ${rowClass}">${supplyDetail.qrCode || ''}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} soluonginbarcode">${supplyDetail.supply.soluongnhap ?? ''} </td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} ghichu">${supplyDetail.supply.note !== null ? supplyDetail.supply.note : ''}</td>
                                    </tr>
                                `;
                                $vatTuDonHangTableBody.append(row);
                            });
                            $orderTitle.text(`Đơn hàng: ${response.order.sodonhang}`);
                            $tongSoVatTu.text(`Tổng vật tư: ${response.totalSupplies}`);
                            $tongDaNhan.text(`Tổng đã nhận: ${response.totalDanhan}`);
                            $tongChuaNhan.text(`Tổng chưa nhận: ${response.totalChuanhan}`);
                            $tongDaXuat.text(`Tổng đã xuất: ${response.totalDaxuat}`);
                        },
                        error: function(xhr, status, error) {
                            console.error('Lỗi khi gửi yêu cầu AJAX:', error);
                            // Hiển thị thông báo lỗi cho người dùng
                        }
                    });
                } else {
                    console.warn('Vui lòng chọn một đơn hàng.');
                }
            };
        });
    </script>
{{-- CAMERA --}}
    <script>
        $(document).ready(function() {
            const $scanButton = $('#scan-button');
            const $barcodeScanner = $('#barcode-scanner');
            const $barcodeInfoContainer = $('#barcode-info-container');
            const $NTDH = $('#NTDH');
            const $actionModal = $('#actionModal');
            const $vatTuDonHangTableBody = $('.vatTuDonHang tbody');

            const $orderTitle = $('#orderTitle');
            const $tongSoVatTu = $('#tongsovattu');
            const $tongDaNhan = $('#tongdanhan');
            const $tongChuaNhan = $('#tongchuanhan');
            const $tongDaXuat = $('#tongdaxuat');

            $scanButton.on('click', function() {
                $barcodeScanner.show();
                $scanButton.hide();
                $NTDH.hide();

                function onScanSuccess(qrCodeMessage) {
                    html5QrCodeScanner.stop().then(ignore => {
                        $actionModal.modal('hide');
                        $actionModal.remove();
                    }).catch(err => {
                        console.error("Failed to stop scanning.", err);
                    });
                    $.ajax({
                        url: "{{ route('layThongTinDonHang') }}",
                        type: 'POST',
                        data: {
                            sodonhang: qrCodeMessage,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $vatTuDonHangTableBody.empty();

                            // Duyệt qua các phần tử trong suppliesDetail và tạo các hàng mới
                            response.suppliesDetail.forEach((supplyDetail, index) => {
                                var totalNhapKho = supplyDetail.viewVatTuChiTietData.reduce((sum, vtct) => sum + vtct.soluongnhapkho, 0);
                                var totalDatChatLuong = supplyDetail.viewVatTuChiTietData.reduce((sum, vtct) => sum + vtct.soluongdatchatluong, 0);

                                // Kiểm tra điều kiện để thêm class blink-warning
                                var rowClass = totalNhapKho > totalDatChatLuong ? 'blink-warning' : '';

                                const row = `
                                    <tr id="supply-row-${supplyDetail.supply.id}" data-id="${supplyDetail.supply.id}">
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} stt">${index + 1}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} tenvattu">${supplyDetail.supply.tenvattu}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} maso">${supplyDetail.supply.maso}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} donvitinh">${supplyDetail.supply.donvitinh}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} soluong">${supplyDetail.supply.soluong}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} soluongnhapkho">${totalNhapKho}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} soluongdatchatluong">${totalDatChatLuong}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} chuanhan">${supplyDetail.chuanhan}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} daxuat">${supplyDetail.daxuat}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} soluonginbarcode">${supplyDetail.supply.soluongnhap ?? ''} </td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} ghichu">${supplyDetail.supply.note !== null ? supplyDetail.supply.note : ''}</td>
                                    </tr>
                                `;
                                $vatTuDonHangTableBody.append(row);
                            });
                            $orderTitle.text(`Đơn hàng: ${response.order.sodonhang}`);
                            $tongSoVatTu.text(`Tổng vật tư: ${response.totalSupplies}`);
                            $tongDaNhan.text(`Tổng đã nhận: ${response.totalDanhan}`);
                            $tongChuaNhan.text(`Tổng chưa nhận: ${response.totalChuanhan}`);
                            $tongDaXuat.text(`Tổng đã xuất: ${response.totalDaxuat}`);
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Đơn hàng không tồn tại',
                            });
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }
                    });
                }

                const html5QrCodeScanner = new Html5Qrcode("barcode-scanner");
                html5QrCodeScanner.start(
                    { facingMode: "environment" },
                    {
                        fps: 10,
                        qrbox: 300 // Tăng kích thước vùng quét QR
                    },
                    onScanSuccess,
                );
            });
        });
    </script>

{{-- HIỂN THỊ LỊCH SỬ --}}
    <script>
        $('.vatTuDonHang tbody').on('click', 'tr', function(event) {
            if ($(event.target).closest('.stt-checkbox').length) {
                return;
            }
            if ($(event.target).closest('.action-btn').length) {
                return;
            }
            if ($(event.target).is('input')) {
                return;
            }
            var supplyId = $(this).data('id'); // Lấy id của vật tư

            $.ajax({
                url:"{{ route('lichsuvattu') }}", // Thay đổi thành URL thực tế của bạn
                type: 'POST',
                data: {
                    id: supplyId, // Gửi ID của vật tư
                    _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                },
                success: function(response) {
                    var htmlContent = '';

                    if (response.message || response.length === 0) {
                        // Nếu server trả về thông báo không có giao dịch hoặc mảng trả về rỗng
                        htmlContent = '<tr><td colspan="9" class="text-center">Chưa có giao dịch cho vật tư này.</td></tr>';
                    } else {
                        // Nếu có dữ liệu giao dịch, xây dựng bảng
                        response.forEach(function(transaction, index) {

                            htmlContent += `<tr>
                                                <td style="text-align: center; vertical-align: middle;">${index + 1}</td>
                                                <td style="text-align: center; vertical-align: middle;">${transaction.supply.tenvattu}</td>
                                                <td style="text-align: center; vertical-align: middle;">${transaction.supply.maso}</td>
                                                <td style="text-align: center; vertical-align: middle;">${transaction.loaigiaodich}</td>
                                                <td style="text-align: center; vertical-align: middle;">${transaction.soluong}</td>
                                                <td style="text-align: center; vertical-align: middle;">${transaction.ngaygiaodich}</td>
                                                <td style="text-align: center; vertical-align: middle;">${transaction.ghichu ? transaction.ghichu : ''}</td>
                                            </tr>`;
                        });
                    }

                    // Đặt htmlContent vào tbody của bảng trong modal
                    $('#historyTransaction .modal-body .table tbody').html(htmlContent);

                    // Hiển thị modal
                    $('#historyTransaction').modal('show');
                },
                error: function(error) {
                    console.log(error);
                    // Hiển thị thông báo lỗi
                    $('#historyTransaction .modal-body').html('<p>Có lỗi xảy ra khi tải dữ liệu.</p>');
                    $('#historyTransaction').modal('show');
                }
            });
        });

    </script>
{{-- HIỂN THỊ CHECKBOX IN MÃ BARCODE--}}
    <script>
        $(document).ready(function() {
            var isCheckboxAdded = false;

            $('#chonVatTu').click(function() {
                if (!isCheckboxAdded) {
                    // Thêm checkbox vào bảng và thay đổi nút "Chọn Vật Tư" thành "Trở Về"
                    $('.vatTuDonHang tbody tr').each(function() {
                        var soluongIn = $(this).find('.soluonginbarcode').text().trim(); // Lấy giá trị từ cột "Số lượng in"
                        if ($(this).find('td.blink-warning').length === 0 && soluongIn === '') {
                            var vattuId = $(this).data('id');
                            var checkboxHtml = '<input type="checkbox" class="form-check-input stt-checkbox" name="selectedItems[]" value="' + vattuId + '">';
                            $(this).find('td:first').data('original-content', $(this).find('td:first').html()).html(checkboxHtml);
                        }
                    });

                    // Tạo và thêm checkbox "Chọn Tất Cả" vào tiêu đề cột STT
                    var headerCheckboxHtml = '<input type="checkbox" class="form-check-input" id="selectAll">';
                    $('.vatTuDonHang thead th:first').data('original-content', $('.vatTuDonHang thead th:first').html()).html(headerCheckboxHtml);

                    isCheckboxAdded = true;
                    $(this).html('<i class="bi bi-arrow-left"></i> Trở Về');

                    // Vô hiệu hóa các nút khác
                    $('button').not(this).not('#printerBarcode').not('.btn-secondary').prop('disabled', true);
                    $('#inVatTu').prop('disabled', false); // Đảm bảo nút "In mã barcode" không bị vô hiệu hóa
                } else {
                    // Khôi phục bảng và nút "Chọn Vật Tư"
                    $('.vatTuDonHang tbody tr').each(function(index) {
                        $(this).find('td').each(function() {
                            var originalContent = $(this).data('original-content');
                            if (originalContent !== undefined) {
                                $(this).html(originalContent);
                            }
                        });
                        $(this).find('td:first').html(index + 1);
                    });

                    // Khôi phục tiêu đề cột STT và loại bỏ checkbox "Chọn Tất Cả"
                    $('.vatTuDonHang thead th:first').html('STT');

                    isCheckboxAdded = false;
                    $(this).html('<i class="bi bi-printer"></i>Chọn Vật Tư');

                    // Ẩn nút In mã barcode
                    $('#inVatTu').hide();

                    // Kích hoạt lại các nút khác
                    $('button').prop('disabled', false);
                }
            });

            // Hiển thị modal khi bấm nút inVatTu
            $('#inVatTu').click(function() {
                updateModalTable();
                $('#basicModal').modal('show');
            });

            // Hàm để thêm dữ liệu vào bảng trong modal
            function updateModalTable() {
                var $tableBody = $('#selectedItemsTable tbody');
                $tableBody.empty(); // Xóa các hàng cũ

                var addedItems = new Set(); // Tạo set để theo dõi các vật tư đã thêm
                var stt = 1; // Biến đếm STT

                $('.stt-checkbox:checked').each(function() {
                    var $row = $(this).closest('tr');
                    var tenVatTu = $row.find('.tenvattu').text().trim();
                    var maSo = $row.find('.maso').text().trim();
                    var maBarcode = $row.find('.barcode').html().trim(); // Lấy HTML của barcode
                    var soluong = parseInt($row.find('.soluong').text().trim(), 10);
                    var soluongdatchatluong = parseInt($row.find('.soluongdatchatluong').text().trim(), 10);

                    // Kiểm tra xem vật tư đã được thêm chưa
                    if (!addedItems.has(maSo)) {
                        addedItems.add(maSo); // Thêm mã số vào set

                        var soLuongInInput = `<input type="number" class="form-control soluongin" value="${soluongdatchatluong}" min="${soluongdatchatluong}" max="${soluong}">`;

                        var newRow = `
                            <tr>
                                <td>${stt++}</td>
                                <td>${tenVatTu}</td>
                                <td>${maSo}</td>
                                <td>${soLuongInInput}</td>
                                <td>${maBarcode}</td>
                            </tr>
                        `;
                        $tableBody.append(newRow);
                    }
                });

                // Ràng buộc giá trị input
                $('.soluongin').on('input', function() {
                    var min = parseInt($(this).attr('min'), 10);
                    var max = parseInt($(this).attr('max'), 10);
                    var value = parseInt($(this).val(), 10);

                    if (value < min) {
                        $(this).val(min);
                    } else if (value > max) {
                        $(this).val(max);
                    }
                });
            }

            // Sự kiện khi giá trị của bất kỳ checkbox nào thay đổi
            $(document).on('change', '.stt-checkbox', function() {
                checkInputsAndToggleButtons();
            });

            // Sự kiện cho checkbox "Chọn Tất Cả"
            $(document).on('change', '#selectAll', function() {
                var isChecked = $(this).is(':checked');
                $('.stt-checkbox').prop('checked', isChecked).trigger('change');
            });

            // Hàm kiểm tra và hiển thị nút
            function checkInputsAndToggleButtons() {
                var anyChecked = $('.stt-checkbox:checked').length > 0;

                // Hiển thị hoặc ẩn các nút dựa trên kết quả kiểm tra
                if (anyChecked) {
                    $('#inVatTu').show();
                } else {
                    $('#inVatTu').hide();
                }
            }

            // Vô hiệu hóa các nút khác khi ở trạng thái "In mã barcode"
            $('button').not('#chonVatTu, #inVatTu, #printerBarcode, .btn-secondary').click(function(e) {
                if (isCheckboxAdded) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Hãy thoát khỏi trạng thái in mã',
                        text: 'Vui lòng trở về từ chế độ in mã trước khi sử dụng chức năng khác.',
                    });
                }
            });

            // Sự kiện cho nút "In mã" trong modal
            $('#printerBarcode').click(function() {
                // Logic cho nút "In mã"
                console.log('In mã barcode');
                // Thêm logic in mã ở đây
            });

            // Sự kiện cho nút "Trở về" trong modal
            $('.btn-secondary').click(function() {
                // Logic cho nút "Trở về"
                console.log('Trở về');
                // Thêm logic trở về ở đây
            });
        });
    </script>

{{-- KIỂM TRA ĐÃ NHẬP MÃ VẬT TƯ CHƯA VÀ IN MÃ LƯU VÀO CSDL--}}
    <script>
        $(document).ready(function() {
                // Sự kiện cho nút "In mã" trong modal
                $('#printerBarcode').click(function() {
                    var isValid = true;
                    var dataToSend = [];

                    // Kiểm tra tất cả các input số lượng in và thu thập dữ liệu
                    $('#selectedItemsTable tbody tr').each(function() {
                        var $row = $(this);
                        var maSo = $row.find('td:nth-child(3)').text().trim();
                        var soLuongIn = parseInt($row.find('.soluongin').val(), 10);

                        // Nếu giá trị bằng 0 hoặc không hợp lệ, đặt isValid thành false
                        if (isNaN(soLuongIn) || soLuongIn === 0) {
                            isValid = false;
                            return false; // Thoát khỏi vòng lặp each
                        }

                        // Thu thập dữ liệu để gửi đi
                        dataToSend.push({
                            maSo: maSo,
                            soLuongIn: soLuongIn
                        });
                    });

                    if (!isValid) {
                        // Hiển thị thông báo nếu có giá trị không hợp lệ
                        Swal.fire({
                            icon: 'warning',
                            title: 'Chưa nhập số lượng in',
                            text: 'Vui lòng nhập số lượng in hợp lệ cho tất cả các vật tư.'
                        });
                    } else {
                        // Gửi dữ liệu qua AJAX
                        $.ajax({
                            url: "{{ route('updateVatTuNhap') }}",
                            type: 'POST',
                            data: {
                                data: dataToSend,
                                _token: '{{ csrf_token() }}' // Thêm token để xác thực
                            },
                            success: function(response) {
                            // Khởi tạo in
                            var iframe = $('<iframe id="printFrame" style="display:none"></iframe>').appendTo('body')[0];
                            var doc = iframe.contentDocument || iframe.contentWindow.document;

                            // Khởi tạo nội dung HTML cần in
                            doc.open();
                            doc.write('<html><head><title>In Barcode</title>');
                            doc.write('<style>');
                            doc.write('@page { size: auto; margin: 10mm; }');
                            doc.write('body { font-family: Arial, sans-serif; font-size: 10pt; }');
                            doc.write('.card { border: 2px solid #007bff; border-radius: 10px; padding: 10px; margin-bottom: 10mm; width: 100%; box-sizing: border-box; page-break-inside: avoid; }');
                            doc.write('.card-body { padding: 0px 20px 0px 25px !important; }');
                            doc.write('.table { width: 100%; border-collapse: collapse; }');
                            doc.write('.table-bordered td { border: 1px solid #dee2e6; padding: 5px; vertical-align: middle; }');
                            doc.write('.text-center { text-align: center; }');
                            doc.write('.align-middle { vertical-align: middle; }');
                            doc.write('</style></head><body>');

                            // Lấy thông tin từ bảng selectedItemsTable
                            $('#selectedItemsTable tbody tr').each(function() {
                                var $row = $(this);
                                var maSo = $row.find('td:nth-child(3)').text().trim();
                                var tenVatTu = $row.find('td:nth-child(2)').text().trim();
                                var donhang = $row.find('td:nth-child(1)').text().trim();
                                var soLuongIn = parseInt($row.find('.soluongin').val(), 10);
                                var barcode = $row.find('td:nth-child(5)').html().trim(); // Lấy HTML của barcode

                                for (var i = 0; i < soLuongIn; i++) {
                                    // Bắt đầu một bảng mới cho mỗi hàng dữ liệu
                                    doc.write('<div class="card">');
                                    doc.write('<div class="card-body">');
                                    doc.write('<div class="row">');
                                    doc.write('<table class="table table-bordered">');
                                    doc.write('<tr>');
                                    doc.write('<td><strong>Mã số:</strong></td>');
                                    doc.write('<td>' + maSo + '</td>');
                                    doc.write('<td rowspan="3" class="text-center align-middle" style="width: 120px;"><div class="qr-code">' + barcode + '</div></td>');
                                    doc.write('</tr>');
                                    doc.write('<tr>');
                                    doc.write('<td><strong>Tên vật tư:</strong></td>');
                                    doc.write('<td>' + tenVatTu + '</td>');
                                    doc.write('</tr>');
                                    doc.write('<tr>');
                                    doc.write('<td><strong>Số đơn hàng:</strong></td>');
                                    doc.write('<td>' + donhang + '</td>');
                                    doc.write('</tr>');
                                    doc.write('</table>');
                                    doc.write('</div>');
                                    doc.write('</div>');
                                    doc.write('</div>');
                                }
                            });

                            doc.write('</body></html>');
                            doc.close();

                            // Focus vào iframe và in nội dung
                            iframe.contentWindow.focus();
                            iframe.contentWindow.print();

                            // Hiển thị thông báo và reload lại trang sau khi in
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Đã in thành công.'
                            }).then(function() {
                                // Reload lại trang
                                location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            // Xử lý lỗi nếu cần
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Có lỗi xảy ra khi gửi dữ liệu.'
                            });
                        }
                    });
                }
            });
        });
    </script>
{{-- HIỂN THỊ CHECKBOX NHẬP KHO --}}
    <script>
        $(document).ready(function() {
            var isNhapKhoCheckboxAdded = false;

            $('#nhapKho').click(function() {
                if (!isNhapKhoCheckboxAdded) {
                    var anyRowHasValue = false;

                    // Thêm checkbox vào các hàng có giá trị trong cột "Số lượng in"
                    $('.vatTuDonHang tbody tr').each(function() {
                        var soluongIn = $(this).find('.soluonginbarcode').text().trim(); // Lấy giá trị từ cột "Số lượng in"
                        if (soluongIn !== '') {
                            anyRowHasValue = true;
                            var vattuId = $(this).data('id');
                            var checkboxHtml = '<input type="checkbox" class="form-check-input nhapKho-checkbox" name="selectedItems[]" value="' + vattuId + '">';
                            $(this).find('td:first').data('original-content', $(this).find('td:first').html()).html(checkboxHtml);
                        }
                    });

                    if (!anyRowHasValue) {
                        // Hiển thị thông báo nếu không có hàng nào có giá trị
                        Swal.fire({
                            icon: 'warning',
                            title: 'Không có vật tư nào',
                            text: 'Hiện tại không có vật tư nào đã được in mã.'
                        });
                    } else {
                        // Tạo và thêm checkbox "Chọn Tất Cả" vào tiêu đề cột STT
                        var headerCheckboxHtml = '<input type="checkbox" class="form-check-input" id="selectAllNhapKho">';
                        $('.vatTuDonHang thead th:first').data('original-content', $('.vatTuDonHang thead th:first').html()).html(headerCheckboxHtml);

                        isNhapKhoCheckboxAdded = true;
                        $(this).html('<i class="bi bi-arrow-left"></i> Trở Về');

                        // Vô hiệu hóa các nút khác ngoại trừ nút "Nhận vật tư"
                        $('button').not(this).not('#nhanVatTu').prop('disabled', true);
                    }
                } else {
                    // Khôi phục bảng và nút "Nhập Kho"
                    $('.vatTuDonHang tbody tr').each(function(index) {
                        $(this).find('td').each(function() {
                            var originalContent = $(this).data('original-content');
                            if (originalContent !== undefined) {
                                $(this).html(originalContent);
                            }
                        });
                        $(this).find('td:first').html(index + 1);
                    });

                    // Khôi phục tiêu đề cột STT và loại bỏ checkbox "Chọn Tất Cả"
                    $('.vatTuDonHang thead th:first').html('STT');

                    isNhapKhoCheckboxAdded = false;
                    $(this).html('<i class="bi bi-box-arrow-in-down"></i> Nhập kho');

                    // Ẩn nút Nhận vật tư
                    $('#nhanVatTu').hide();

                    // Kích hoạt lại các nút khác
                    $('button').prop('disabled', false);
                }
            });

            // Sự kiện cho checkbox "Chọn Tất Cả" cho nhập kho
            $(document).on('change', '#selectAllNhapKho', function() {
                var isChecked = $(this).is(':checked');
                $('.nhapKho-checkbox').prop('checked', isChecked).trigger('change');
            });

            // Sự kiện khi giá trị của bất kỳ checkbox nào thay đổi
            $(document).on('change', '.nhapKho-checkbox', function() {
                checkCheckboxesAndToggleNhanVatTuButton();
            });

            function checkCheckboxesAndToggleNhanVatTuButton() {
                var anyChecked = $('.nhapKho-checkbox:checked').length > 0;

                // Hiển thị hoặc ẩn nút Nhận vật tư dựa trên kết quả kiểm tra
                if (anyChecked) {
                    $('#nhanVatTu').show();
                    $('#nhanVatTu').prop('disabled', false); // Đảm bảo nút không bị vô hiệu hóa
                } else {
                    $('#nhanVatTu').hide();
                }
            }
        });
    </script>
{{-- CHUYỂN HƯỚNG SANG TRANG NHẬP KHO --}}
    <script>
        $(document).ready(function() {
            // Sự kiện cho nút "Nhận vật tư"
            $('#nhanVatTu').click(function(e) {
                e.preventDefault(); // Ngăn chặn hành động mặc định

                var selectedItems = [];
                var itemMap = {}; // Sử dụng đối tượng để theo dõi các mục đã chọn
                // Lấy thông tin từ các hàng có checkbox được chọn
                $('.nhapKho-checkbox:checked').each(function() {
                    var row = $(this).closest('tr'); // Lấy hàng chứa checkbox đang được chọn
                    var soLuongNhapKho = row.find('td').eq(10).text().trim(); // Lấy giá trị từ cột "Số lượng in"
                    var itemId = $(this).val();

                    // Kiểm tra nếu phần tử đã tồn tại trong itemMap
                    if (!itemMap[itemId]) {
                        itemMap[itemId] = true; // Đánh dấu là đã thêm
                        selectedItems.push({
                            id: itemId,
                            soLuongNhapKho: soLuongNhapKho
                        });
                    }
                });

                // Kiểm tra nếu không có mục nào được chọn
                if (selectedItems.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Chưa chọn vật tư',
                        text: 'Vui lòng chọn ít nhất một vật tư để nhập kho.'
                    });
                    return false;
                }
                        var baseUrl = "{{ route('quetBarcodeNhapKho') }}"; // Đảm bảo đường dẫn này được in đúng trong mã HTML của bạn
                        var query = $.param({ 'selectedItems': selectedItems });
                        var redirectUrl = baseUrl + '?' + query;

                        window.location.href = redirectUrl;
            });
        });
    </script>
@endsection
