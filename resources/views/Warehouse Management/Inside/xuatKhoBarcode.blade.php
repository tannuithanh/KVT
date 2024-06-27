@extends('Layout.app')
@section('style')
<style>
    .fixed-barcode-scanner {
            position: sticky; /* Thay đổi từ fixed sang sticky */
            top: 10px; /* Khoảng cách từ top màn hình khi thành sticky */
            z-index: 12; /* Đảm bảo nó luôn trên cùng */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            height: 320px;
            width: 300px;
            border: 3px solid #ddd;
            overflow: hidden;
        }
</style>
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/toastify.min.css')}}">
@endsection
@section('title')
    Xuất kho barcode
@endsection

@section('content')
<div class="pagetitle">
    <h1>Quét barcode xuất kho</h1>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <button id="scan-button" class="btn btn-primary mt-3" style="width: 100%; margin-bottom: 20px;">Khởi động camera</button>
                    <!-- Set a fixed height for the scanner container to prevent it from expanding -->
                    <div id="barcode-scanner" class="fixed-barcode-scanner">
                        <video id="video" style="width: 100%; height: 100%; object-fit: contain;" autoplay></video>
                    </div>
                    <div id="barcode-info-container" class="mt-4">

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('script')
<script src="{{asset('assets/js/html5-qrcode.min.js')}}"></script>
<script src="{{ asset('assets/js/toastify-js.js') }}"></script>
{{-- CAMERA --}}
    <script>
        var scannedBarcodes = [];
        var allowedMasos = @json($maso);

        document.addEventListener('DOMContentLoaded', function () {
            const scanButton = document.getElementById('scan-button');
            const barcodeScanner = document.getElementById('barcode-scanner');
            const barcodeInfoContainer = document.getElementById('barcode-info-container');

            scanButton.addEventListener('click', function () {
                barcodeScanner.style.display = 'block';  // Always show the scanner
                scanButton.style.display = 'none';  // Hide the button after starting

                function onScanSuccess(qrCodeMessage) {
                    if (!scannedBarcodes.includes(qrCodeMessage) && allowedMasos.includes(qrCodeMessage)) {
                        scannedBarcodes.push(qrCodeMessage);

                        $.ajax({
                            url: "{{ route('checkVatTuXuatKho') }}",
                            type: 'POST',
                            data: { barcode: qrCodeMessage },
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            success: function(data) {
                                if (data.exists && data.remaining > 0) {
                                    var cardHtml = `
                                        <div class="card mb-3" style="border-radius: 15px; background-color: #007bff; color: white;">
                                            <div class="card-header" style="background-color: #0056b3;color: white; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                                Đơn hàng: ${data.orderName}
                                            </div>
                                            <div class="card-body" style="background-color: #007bff;">
                                                <h5 class="card-title">${data.name}</h5>
                                                <p class="card-text">Số lượng còn lại: ${data.remaining}</p>
                                                <input type="number" class="form-control" placeholder="Nhập số lượng muốn xuất" min="1" max="${data.remaining}" id="input-quantity-${data.id}">
                                                <button class="btn btn-light mt-2 confirm-export" data-id="${data.id}">Xác nhận xuất kho</button>
                                            </div>
                                        </div>`;
                                    barcodeInfoContainer.innerHTML += cardHtml;

                                    Toastify({
                                        text: `Vật tư đã sẵn sàng xuất kho`,
                                        duration: 3000,
                                        close: true,
                                        gravity: "top",
                                        position: "right",
                                        backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                                        className: "info",
                                    }).showToast();
                                } else {
                                    Toastify({
                                        text: `Lỗi: ${data.message}`,
                                        duration: 3000,
                                        close: true,
                                        gravity: "top",
                                        position: "right",
                                        backgroundColor: "linear-gradient(to right, #ff5f5f, #d33d3d)",
                                        className: "info",
                                    }).showToast();
                                }
                            },
                            error: function(error) {
                                console.error('Error:', error);
                                Toastify({
                                    text: 'Có lỗi xảy ra khi kiểm tra vật tư!',
                                    duration: 3000,
                                    close: true,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "linear-gradient(to right, #ff5f5f, #d33d3d)",
                                    className: "info",
                                }).showToast();
                            }
                        });
                    } else if (!allowedMasos.includes(qrCodeMessage)) {
                        Toastify({
                            text: 'Mã không khớp với danh sách cho phép.',
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            backgroundColor: "linear-gradient(to right, #ff5f5f, #d33d3d)",
                            className: "info",
                        }).showToast();
                    }
                }

                function onScanFailure(error) {
                    console.warn(`QR error: ${error}`);
                }

                const html5QrCode = new Html5Qrcode("barcode-scanner");
                html5QrCode.start(
                    { facingMode: "environment" },
                    {
                        fps: 10,
                        qrbox: 250
                    },
                    onScanSuccess,
                    onScanFailure
                ).catch(err => {
                    console.error(`Unable to start scanning, error: ${err}`);
                });
            });
        });
    </script>


{{-- XÁC NHẬN XUẤT KHO --}}
    <script>
        $(document).ready(function() {
            $('body').on('click', '.confirm-export', function() {
                var button = $(this);
                var supplyId = button.data('id');
                var quantity = $('#input-quantity-' + supplyId).val();
                var maxQuantity = $('#input-quantity-' + supplyId).attr('max');

                if (quantity && quantity > 0 && quantity <= maxQuantity) {
                    $.ajax({
                        url: '{{ route("xacNhanXuatKho") }}',
                        type: 'POST',
                        data: { id: supplyId, quantity: quantity },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            if(response.success) {
                                var cardBody = button.closest('.card-body');
                                cardBody.find('.card-text').text('Số lượng còn lại: ' + response.remaining);
                                button.siblings('input').remove(); // Xóa input số lượng
                                button.remove(); // Xóa nút xác nhận xuất kho
                                Toastify({
                                    text: 'Thành công! Xuất kho thành công.',
                                    duration: 3000,
                                    close: true,
                                    gravity: 'top',
                                    position: 'right',
                                    backgroundColor: 'linear-gradient(to right, #00b09b, #96c93d)',
                                    className: 'info',
                                }).showToast();
                            } else {
                                Toastify({
                                    text: `Lỗi! ${response.message}`,
                                    duration: 3000,
                                    close: true,
                                    gravity: 'top',
                                    position: 'right',
                                    backgroundColor: 'linear-gradient(to right, #ff5f5f, #d33d3d)',
                                    className: 'info',
                                }).showToast();
                            }
                        },
                        error: function(xhr, status, error) {
                            Toastify({
                                text: `Lỗi! Có lỗi xảy ra: ${error}`,
                                duration: 3000,
                                close: true,
                                gravity: 'top',
                                position: 'right',
                                backgroundColor: 'linear-gradient(to right, #ff5f5f, #d33d3d)',
                                className: 'info',
                            }).showToast();
                        }
                    });
                } else {
                    Toastify({
                        text: 'Thông báo! Vui lòng nhập số lượng hợp lệ không quá số lượng còn lại.',
                        duration: 3000,
                        close: true,
                        gravity: 'top',
                        position: 'right',
                        backgroundColor: 'linear-gradient(to right, #ff5f5f, #d33d3d)',
                        className: 'info',
                    }).showToast();
                }
            });
        });
    </script>


@endsection
