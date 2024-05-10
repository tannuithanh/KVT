@extends('Layout.app')
@section('style')
<style>
    .fixed-barcode-scanner {
        position: sticky; /* Thay đổi từ fixed sang sticky */
        top: 10px; /* Khoảng cách từ top màn hình khi thành sticky */
        z-index: 1000; /* Đảm bảo nó luôn trên cùng */
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        border-radius: 8px;
        height: 200px;
        width: 320px;
        border: 3px solid #ddd;
        overflow: hidden;
    }
</style>
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
                    <button id="scan-button" class="btn btn-primary" style="width: 100%; margin-bottom: 20px;">Khởi động camera</button>
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
<script src="{{ asset('assets/js/quagga.min.js') }}"></script>
{{-- CAMERA --}}
    <script>
        var scannedBarcodes = [];

        document.addEventListener('DOMContentLoaded', function () {
            const scanButton = document.getElementById('scan-button');
            const barcodeScanner = document.getElementById('barcode-scanner');
            const barcodeInfoContainer = document.getElementById('barcode-info-container');

            scanButton.addEventListener('click', function () {
                barcodeScanner.style.display = 'block';  // Always show the scanner
                scanButton.style.display = 'none';  // Hide the button after starting

                Quagga.init({
                    inputStream: {
                        name: "Live",
                        type: "LiveStream",
                        target: barcodeScanner,
                        constraints: {
                            facingMode: "environment"
                        }
                    },
                    decoder: {
                        readers: ["code_128_reader"]
                    }
                }, function (err) {
                    if (err) {
                        console.error("Cannot initialize QuaggaJS", err);
                        return;
                    }
                    Quagga.start();
                });

                Quagga.onDetected(function(result) {
                    var barcode = result.codeResult.code;
                    if (!scannedBarcodes.includes(barcode)) {
                        scannedBarcodes.push(barcode);
                        $.ajax({
                            url: "{{ route('checkVatTuXuatKho') }}",
                            type: 'POST',
                            data: { barcode: barcode },
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
                                } else {
                                    alert(data.message);
                                }
                            },
                            error: function(error) {
                                console.error('Error:', error);
                                alert('Có lỗi xảy ra khi kiểm tra vật tư!');
                            }
                        });
                    }
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
                                Swal.fire('Thành công!', 'Xuất kho thành công.', 'success');
                            } else {
                                Swal.fire('Lỗi!', response.message, 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire('Lỗi!', 'Có lỗi xảy ra: ' + error, 'error');
                        }
                    });
                } else {
                    Swal.fire('Thông báo', 'Vui lòng nhập số lượng hợp lệ không quá số lượng còn lại.', 'warning');
                }
            });
        });

    </script>

@endsection
