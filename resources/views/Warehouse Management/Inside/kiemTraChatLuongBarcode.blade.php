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
    <style>
        .select2-container {
            width: 100% !important;
        }
        .select2-selection {
            height: calc(2.25rem + 2px) !important;
            padding: 0.375rem 0.75rem !important;
        }
        .select2-selection__rendered {
            line-height: 1.25rem !important;
        }
        .select2-selection__arrow {
            height: calc(2.25rem + 2px) !important;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/toastify.min.css')}}">
    <link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" />
@endsection
@section('content')
    <div class="pagetitle">
        <h1>Kiểm tra chất lượng barcode</h1>
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
                            <select id="manual-input" class="form-control mb-3"></select>
                            <button id="manual-submit" class="btn btn-success w-100 mt-3">Xác nhận</button>
                        </div>
                        <div id="Danhmucvattuxuat" class="mt-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script src="{{ asset('assets/js/html5-qrcode.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/toastify-js.js') }}"></script>
    <script src="{{asset('assets/js/select2.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            var supplies = @json($supplies);
            console.log(supplies);
            var html5QrCode = new Html5Qrcode("barcode-scanner");

            // Initialize Select2
            $('#manual-input').select2({
                placeholder: 'Chọn mã số vật tư',
                width: '100%',
                data: supplies.map(supply => ({
                    id: supply.maso_new ? supply.maso_new : supply.maso,
                    text: (supply.maso_new ? supply.maso_new : supply.maso) + ' - ' + supply.tenvattu
                }))
            });

            $('#scan-button').on('click', function () {
                if (html5QrCode) {
                    $('#barcode-scanner').show();
                    $('#manual-entry').hide();

                    html5QrCode.start(
                        { facingMode: "environment" },
                        {
                            fps: 10,
                            qrbox: { width: 250, height: 250 }
                        },
                        function onScanSuccess(decodedText, decodedResult) {
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
                } else {
                    showError('Thư viện Html5QrCode chưa được tải.');
                }
            });

            $('#manual-button').on('click', function () {
                $('#barcode-scanner').hide();
                $('#manual-entry').show();
            });

            $('#manual-submit').on('click', function () {
                var selectedValue = $('#manual-input').val();
                var matchedSupply = supplies.find(supply => supply.maso_new === selectedValue || supply.maso === selectedValue);

                if (matchedSupply) {
                    displaySupplyCard(matchedSupply);
                } else {
                    showError('Không tìm thấy vật tư nào khớp.');
                }
            });

            function displaySupplyCard(supply) {
                var cardHtml = `
                    <div class="card mb-3 supply-card" id="supply-card-${supply.id}" style="border-radius: 15px; background-color: #007bff; color: white;">
                        <div class="card-header" style="background-color: #0056b3; color: white; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                            Đơn hàng: ${supply.orders.length > 0 ? supply.orders[0].sodonhang : 'Không có đơn hàng'}
                        </div>
                        <div class="card-body" style="background-color: #007bff;">
                            <h5 class="card-title">${supply.tenvattu}</h5>
                            <p class="card-text">Mã số: ${supply.maso}</p>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="card-text">Số lượng đạt chất lượng:
                                        <input class="form-control" type="number" name="soluongDatChatLuong" min="0" max="${supply.soluong}" value="${supply.soluong}" id="soluongDatChatLuong-${supply.id}">
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="card-text">Số lượng không đạt chất lượng:
                                        <input class="form-control" type="number" name="soluongKhongDatChatLuong" min="0" max="${supply.soluong}" value="0" id="soluongKhongDatChatLuong-${supply.id}">
                                    </p>
                                </div>
                            </div>
                            <p class="card-text d-none" id="nguyennhan-container-${supply.id}">Nguyên nhân:
                                <textarea class="form-control" name="nguyennhan" id="nguyennhan-${supply.id}" rows="3"></textarea>
                            </p>
                            <button class="btn btn-success" onclick="submitData(${supply.id})">Hoàn tất</button>
                        </div>
                    </div>
                `;
                $('#Danhmucvattuxuat').html(cardHtml);

                var $datChatLuong = $(`#soluongDatChatLuong-${supply.id}`);
                var $khongDatChatLuong = $(`#soluongKhongDatChatLuong-${supply.id}`);
                var $nguyenNhanContainer = $(`#nguyennhan-container-${supply.id}`);

                function updateValues() {
                    var datChatLuongValue = parseInt($datChatLuong.val()) || 0;
                    var khongDatChatLuongValue = supply.soluong - datChatLuongValue;

                    if (datChatLuongValue < 0) datChatLuongValue = 0;
                    if (datChatLuongValue > supply.soluong) datChatLuongValue = supply.soluong;
                    if (khongDatChatLuongValue < 0) khongDatChatLuongValue = 0;
                    if (khongDatChatLuongValue > supply.soluong) khongDatChatLuongValue = supply.soluong;

                    $datChatLuong.val(datChatLuongValue);
                    $khongDatChatLuong.val(khongDatChatLuongValue);

                    $nguyenNhanContainer.toggleClass('d-none', khongDatChatLuongValue <= 0);
                }

                $datChatLuong.on('input', updateValues);
                $khongDatChatLuong.on('input', updateValues);
            }

            window.submitData = function (id) {
                var quantityDat = $(`#soluongDatChatLuong-${id}`).val();
                var quantityKhongDat = $(`#soluongKhongDatChatLuong-${id}`).val();
                var note = $(`#nguyennhan-${id}`).val();

                if (quantityDat && !isNaN(quantityDat) && quantityKhongDat && !isNaN(quantityKhongDat)) {
                    if (parseInt(quantityKhongDat) > 0 && (!note || note.trim() === "")) {
                        showError('Bạn phải nhập vào nguyên nhân không đạt chất lượng.');
                        return;
                    }

                    $.ajax({
                        url: "{{ route('luuKiemTraChatLuong') }}",
                        type: "POST",
                        data: {
                            id: id,
                            quantityDat: parseInt(quantityDat),
                            quantityKhongDat: parseInt(quantityKhongDat),
                            note: note,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            if (response.success) {
                                Toastify({
                                    text: response.message,
                                    duration: 5000,
                                    close: true,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                                    className: "info",
                                }).showToast();
                                $(`#supply-card-${id}`).remove();  // Xóa thẻ div sau khi hoàn thành tác vụ
                            } else {
                                showError(response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Có lỗi xảy ra: ', error);
                            let message = xhr.responseJSON ? xhr.responseJSON.message : "Có lỗi xảy ra khi cập nhật";
                            showError(message);
                        }
                    });
                } else {
                    showError('Vui lòng nhập số lượng hợp lệ.');
                }
            };

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






