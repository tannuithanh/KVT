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
    Xuất kho
@endsection
@section('content')
<div class="pagetitle">
    <h1>Xuất kho</h1>
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
                    <div class="table-responsive" style="max-height: 550px;">
                        <table class="table table-borderless table-bordered table-hover mt-2 vattuchitiet fixed-header">
                            <thead>
                                    <tr>
                                        <th style="text-align: center;display:none;" scope="col"> Chọn </th>
                                        <th style="text-align: center" scope="col">Stt</th>
                                        <th style="text-align: center" scope="col">Đơn hàng</th>
                                        <th style="text-align: center" scope="col">Tên vật tư</th>
                                        <th style="text-align: center" scope="col">Mã vật tư</th>
                                        <th style="text-align: center" scope="col">số lượng còn</th>
                                        <th style="text-align: center" scope="col">Chi Phí</th>
                                        <th style="text-align: center" scope="col">Ghi chú</th>
                                    </tr>
                            </thead>
                        </table>
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
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} donvitinh">${supplyDetail.supply.donvitinh}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} soluong">${supplyDetail.supply.soluong}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} soluongnhapkho">${totalNhapKho}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} soluongdatchatluong">${totalDatChatLuong}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} chuanhan">${supplyDetail.chuanhan}</td>
                                        <td style="text-align: center; vertical-align: middle" class="${rowClass} daxuat">${supplyDetail.daxuat}</td>
                                        <td style="text-align: center; vertical-align: middle" class="barcode ${rowClass}">${supplyDetail.qrCode || ''}</td>
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
                                        <td class="barcode ${rowClass}">${supplyDetail.barcodeHtml || ''}<div>${supplyDetail.supply.maso || ''}</div></td>
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

@endsection
