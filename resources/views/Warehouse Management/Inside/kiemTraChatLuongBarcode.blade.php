@extends('Layout.app')
@section('style')
    <style>
        .fixed-barcode-scanner {
            position: sticky; /* Thay đổi từ fixed sang sticky */
            top: 10px; /* Khoảng cách từ top màn hình khi thành sticky */
            z-index: 12; /* Đảm bảo nó luôn trên cùng */
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            height: 200px;
            width: 320px;
            border: 3px solid #ddd;
            overflow: hidden;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/toastify.min.css')}}">
@endsection
@section('content')
<div class="pagetitle">
    <h1>Quét barcode kiểm tra chất lượng</h1>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <button id="scan-button" class="btn btn-primary" style="width: 100%; margin-bottom: 20px;">Khởi động camera</button>
                    <div id="barcode-scanner" class="fixed-barcode-scanner">
                        <video id="video" style="width: 100%; height: 100%; object-fit: contain;" autoplay></video>
                    </div>
                    <div id="Danhmucvattuxuat" class="mt-4">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@section('script')
    <script src="{{ asset('assets/js/quagga.min.js') }}"></script>
    <script src="{{ asset('assets/js/toastify-js.js') }}"></script>
    <script>
                function submitData(id) {
                    var idVatTu = id;
                    var quantity = document.getElementById('soluongDatChatLuong-' + id).value;
                    $.ajax({
                        type: 'POST',
                        url: '{{ route("luuKiemTraChatLuong") }}',
                        data: {
                            idQualityCheck: id,
                            quantityDat: quantity,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.success) {
                                Toastify({
                                    text: `Kiểm tra chất lượng thành công`,
                                    duration: 3000,
                                    close: true,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                                    className: "info",
                                }).showToast();
                                var input = document.getElementById('soluongDatChatLuong-' + idVatTu);
                                var button = document.querySelector(`button[onclick='submitData(${idVatTu})']`);
                                var value = input.value;
                                input.style.display = 'none';
                                button.style.display = 'none';
                                var parentParagraph = input.parentNode;
                                parentParagraph.textContent = `Số lượng đạt chất lượng: ${value}`;
                                                    } else {
                                Toastify({
                                    text: `Lỗi: ${response.message}`,
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
                                text: 'Có lỗi xảy ra khi gửi dữ liệu!',
                                duration: 3000,
                                close: true,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "linear-gradient(to right, #ff5f5f, #d33d3d)",
                                className: "info",
                            }).showToast();
                        }
                    });
                }
                document.addEventListener('DOMContentLoaded', function () {
                    document.querySelector('#scan-button').addEventListener('click', function () {
                        Quagga.init({
                            inputStream: {
                                name: "Live",
                                type: "LiveStream",
                                target: document.querySelector('#barcode-scanner'),
                                constraints: {
                                    facingMode: "environment"
                                }
                            },
                            decoder: {
                                readers: ["code_128_reader"]
                            }
                        }, function (err) {
                            if (err) {
                                console.log(err);
                                alert("Không khởi tạo được QuaggaJS: " + err);
                                return;
                            }
                            console.log("Initialization finished. Ready to start");
                            Quagga.start();
                        });

                        var scannedBarcodes = []; // Mảng để lưu trữ các mã đã quét
                        Quagga.onDetected(function(data) {
                            var mavattu = data.codeResult.code;

                            // Kiểm tra xem mã đã được quét trước đó chưa
                            if (scannedBarcodes.includes(mavattu)) {
                                return; // Dừng xử lý nếu mã đã quét
                            }

                            // Thêm mã vào mảng các mã đã quét
                            scannedBarcodes.push(mavattu);

                            $.ajax({
                                type: 'POST',
                                url: '{{ route("takeIdByBarcode") }}',
                                data: {
                                    mavattu: mavattu,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {

                                    if (response.status === 'Thành công') {
                                        var matchedSupply = response.data;
                                        // alert('Success: ' + JSON.stringify(matchedSupply.id));
                                        var cardHtml = `
                                            <div class="card mb-3" style="border-radius: 15px; background-color: #007bff; color: white;">
                                                <div class="card-header" style="background-color: #0056b3;color: white; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                                    Đơn hàng: ${matchedSupply.order.sodonhang}
                                                </div>
                                                <div class="card-body" style="background-color: #007bff;">
                                                    <h5 class="card-title">${matchedSupply.tenvattu}</h5>
                                                    <p class="card-text">Mã số: ${matchedSupply.maso}</p>
                                                    <p class="card-text">Số lượng đạt chất lượng:
                                                        <input class="form-control" type="number" name="soluongDatChatLuong" min="0" id="soluongDatChatLuong-${matchedSupply.id}">
                                                    </p>
                                                    <button class="btn btn-success" onclick="submitData(${matchedSupply.id})">Hoàn tất</button>
                                                </div>
                                            </div>`;
                                        document.querySelector('#Danhmucvattuxuat').innerHTML += cardHtml;
                                    } else {
                                        alert(response.message);
                                    }
                                },
                                error: function(error) {
                                    console.error('Error:', error);
                                    alert('Có lỗi xảy ra khi truy vấn dữ liệu!');
                                }
                            });
                        });

                    });
                });

    </script>
@endsection





