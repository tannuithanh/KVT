@extends('Layout.app')
@section('style')
    <style>
        .fixed-barcode-scanner {
            position: sticky;
            top: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            height: 300px;
            width: 100%;
            max-width: 320px;
            border: 3px solid #ddd;
            overflow: hidden;
            margin: 0 auto;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/toastify.min.css')}}">
@endsection
@section('content')
    <div class="pagetitle">
        <h1>Quét barcode nhập kho</h1>
    </div>
    <section class="section">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3 mt-3">
                            <button id="scan-button" class="btn btn-primary w-45">Khởi động camera</button>
                            <button id="manual-button" class="btn btn-secondary w-45">Nhập thủ công</button>
                        </div>
                        <div id="barcode-scanner" class="fixed-barcode-scanner" style="display: none;">
                            <video id="video" style="width: 100%; height: 100%; object-fit: contain;" autoplay></video>
                        </div>
                        <div id="manual-entry" class="mt-4" style="display: none;">
                            <input type="text" id="manual-input" class="form-control mb-3" placeholder="Nhập mã số vật tư">
                            <button id="manual-submit" class="btn btn-success w-100">Xác nhận</button>
                        </div>
                        <div id="Danhmucvattuxuat" class="mt-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('script')
    {{-- <script src="{{ asset('assets/js/quagga.min.js') }}"></script> --}}
    <script src="{{asset('assets/js/html5-qrcode.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('assets/js/toastify-js.js')}}"></script>
{{-- QUÉT QR KHI NHẬP KHO--}}
    <script>
        $(document).ready(function () {
            var supplies = @json($supplies);

            var html5QrCode = new Html5Qrcode("barcode-scanner");

            $('#scan-button').on('click', function () {
                $('#barcode-scanner').show();
                $('#manual-entry').hide();

                html5QrCode.start(
                    { facingMode: "environment" },
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 }
                    },
                    function onScanSuccess(decodedText, decodedResult) {
                        $('#Danhmucvattuxuat').text(`Mã quét được: ${decodedText}`);

                        var matchedSupply = supplies.find(supply => supply.maso === decodedText || supply.maso_new === decodedText);

                        if (matchedSupply) {
                            displaySupplyCard(matchedSupply);
                        } else {
                            showError('Không tìm thấy vật tư nào khớp.');
                        }
                    }
                ).catch(err => {
                    console.log(`Lỗi khi khởi tạo quét mã QR: ${err}`);
                });
            });

            $('#manual-button').on('click', function () {
                $('#barcode-scanner').hide();
                $('#manual-entry').show();
            });

            $('#manual-input').on('input', function () {
                $(this).val($(this).val().toUpperCase());
            });

            $('#manual-submit').on('click', function () {
                var inputValue = $('#manual-input').val().toUpperCase();
                var matchedSupply = supplies.find(supply => supply.maso_new ? supply.maso_new === inputValue : supply.maso === inputValue);

                if (matchedSupply) {
                    displaySupplyCard(matchedSupply);
                } else {
                    showError('Không tìm thấy vật tư nào khớp.');
                }
            });

            function displaySupplyCard(supply) {
                var cardHtml = `
                    <div class="card mb-3" id="supply-card">
                        <div class="card-header">
                            Đơn hàng: ${supply.orders.length > 0 ? supply.orders[0].sodonhang : 'Không có đơn hàng'}
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">${supply.tenvattu}</h5>
                            <p class="card-text">Mã số: ${supply.maso}</p>
                            <p class="card-text">
                                Số lượng nhập kho:
                                <input type="number" id="quantity-input" class="form-control" placeholder="Nhập số lượng">
                            </p>
                            <button id="nhap-kho-button" class="btn btn-success">Nhập kho</button>
                        </div>
                    </div>
                `;
                $('#Danhmucvattuxuat').html(cardHtml);

                $('#nhap-kho-button').on('click', function () {
                    var quantity = $('#quantity-input').val();
                    if (quantity && !isNaN(quantity)) {
                        $.ajax({
                            url: "{{ route('updateQuanlity') }}",
                            type: "POST",
                            data: {
                                id: supply.id,
                                quantity: parseInt(quantity),
                                _token: '{{ csrf_token() }}'
                            },
                            success: function (response) {
                                if (response.success) {
                                    Toastify({
                                        text: `Nhập kho thành công: ${supply.tenvattu}`,
                                        duration: 5000,
                                        close: true,
                                        gravity: "top",
                                        position: "right",
                                        backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                                        className: "info",
                                    }).showToast();
                                    $('#supply-card').remove();
                                } else if (response.error) {
                                    showError(response.error);
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error('Có lỗi xảy ra: ', error);
                                let message = xhr.responseJSON ? xhr.responseJSON.error : "Có lỗi xảy ra khi cập nhật";
                                showError(message);
                            }
                        });
                    } else {
                        showError('Vui lòng nhập số lượng hợp lệ.');
                    }
                });
            }

            function showError(message) {
                Toastify({
                    text: message,
                    duration: 5000,
                    close: true,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
                    className: "error",
                }).showToast();
            }
        });
    </script>



@endsection





