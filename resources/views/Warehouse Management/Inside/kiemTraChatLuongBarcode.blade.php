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
    <script src="{{ asset('assets/js/html5-qrcode.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/js/toastify-js.js') }}"></script>
    <script>
        $(document).ready(function () {
            var supplies = @json($supplies);
            var html5QrCode;

            // Kiểm tra xem thư viện Html5QrCode đã được tải chưa
            if (typeof Html5QrCode !== 'undefined') {
                html5QrCode = new Html5QrCode("barcode-scanner");
            } else {
                console.error('Thư viện Html5QrCode chưa được tải.');
            }

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
                    <div class="card mb-3 supply-card" id="supply-card-${supply.id}" style="border-radius: 15px; background-color: #007bff; color: white;">
                        <div class="card-header" style="background-color: #0056b3; color: white; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                            Đơn hàng: ${supply.orders.length > 0 ? supply.orders[0].sodonhang : 'Không có đơn hàng'}
                        </div>
                        <div class="card-body" style="background-color: #007bff;">
                            <h5 class="card-title">${supply.tenvattu}</h5>
                            <p class="card-text">Mã số: ${supply.maso}</p>
                            <p class="card-text">Số lượng đạt chất lượng:
                                <input class="form-control" type="number" name="soluongDatChatLuong" min="0" id="soluongDatChatLuong-${supply.id}">
                            </p>
                            <p class="card-text">Ghi chú:
                                <textarea class="form-control" name="ghichu" id="ghichu-${supply.id}" rows="3"></textarea>
                            </p>
                            <button class="btn btn-success" onclick="submitData(${supply.id})">Hoàn tất</button>
                        </div>
                    </div>
                `;
                $('#Danhmucvattuxuat').html(cardHtml);
            }

            window.submitData = function (id) {
                var quantity = $(`#soluongDatChatLuong-${id}`).val();
                var note = $(`#ghichu-${id}`).val();

                if (quantity && !isNaN(quantity)) {
                    $.ajax({
                        url: "{{ route('luuKiemTraChatLuong') }}",
                        type: "POST",
                        data: {
                            id: id,
                            quantity: parseInt(quantity),
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






