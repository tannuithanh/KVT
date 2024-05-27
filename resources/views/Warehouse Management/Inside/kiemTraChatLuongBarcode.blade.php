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

    <script src="{{asset('assets/js/html5-qrcode.min.js')}}"></script>
    <script src="{{ asset('assets/js/toastify-js.js') }}"></script>
    <script>
        var masos = @json($masos);

        function submitData(id) {
            var idVatTu = id;
            var quantity = document.getElementById('soluongDatChatLuong-' + id).value;
            var ghiChu = document.getElementById('ghichu-' + id).value;
            alert(ghiChu);
            $.ajax({
                type: 'POST',
                url: '{{ route("luuKiemTraChatLuong") }}',
                data: {
                    idQualityCheck: id,
                    quantityDat: quantity,
                    ghiChu: ghiChu,
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
                        var textarea = document.getElementById('ghichu-' + idVatTu);
                        var button = document.querySelector(`button[onclick='submitData(${idVatTu})']`);
                        var value = input.value;
                        input.style.display = 'none';
                        textarea.style.display = 'none';
                        button.style.display = 'none';
                        var parentParagraph = input.parentNode;
                        parentParagraph.textContent = `Số lượng đạt chất lượng: ${value}`;
                        var parentParagraphNote = textarea.parentNode;
                        parentParagraphNote.textContent = `Ghi chú: ${ghiChu}`;
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
            var html5QrCode = new Html5Qrcode("barcode-scanner");

            document.querySelector('#scan-button').addEventListener('click', function () {
                const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                    if (scannedBarcodes.includes(decodedText)) {
                        console.log('Mã này đã được quét và xử lý.');
                        return;
                    }

                    if (!masos.includes(decodedText)) {
                        Toastify({
                            text: "Vật tư không nằm trong đơn hàng",
                            duration: 3000,
                            gravity: "top",
                            position: "center",
                            backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)"
                        }).showToast();
                        return;
                    }

                    scannedBarcodes.push(decodedText);

                    $.ajax({
                        type: 'POST',
                        url: '{{ route("takeIdByBarcode") }}',
                        data: {
                            mavattu: decodedText,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status === 'Thành công') {
                                var matchedSupply = response.data;
                                var cardHtml = `
                                    <div class="card mb-3" style="border-radius: 15px; background-color: #007bff; color: white;">
                                        <div class="card-header" style="background-color: #0056b3; color: white; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                            Đơn hàng: ${matchedSupply.order.sodonhang}
                                        </div>
                                        <div class="card-body" style="background-color: #007bff;">
                                            <h5 class="card-title">${matchedSupply.tenvattu}</h5>
                                            <p class="card-text">Mã số: ${matchedSupply.maso}</p>
                                            <p class="card-text">Số lượng đạt chất lượng:
                                                <input class="form-control" type="number" name="soluongDatChatLuong" min="0" id="soluongDatChatLuong-${matchedSupply.id}">
                                            </p>
                                            <p class="card-text">Ghi chú:
                                                <textarea class="form-control" name="ghichu" id="ghichu-${matchedSupply.id}" rows="3"></textarea>
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
                };

                const qrCodeErrorCallback = (errorMessage) => {
                    console.log(`QR Code no longer in front of camera. (${errorMessage})`);
                };

                const config = { fps: 10, qrbox: 250 };

                if (html5QrCode.isScanning) {
                    html5QrCode.stop().then((ignore) => {
                        console.log("Quét đã dừng.");
                    }).catch((err) => {
                        console.log("Không thể dừng quét.", err);
                    });
                }

                html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback, qrCodeErrorCallback);
            });

            var scannedBarcodes = [];
        });
    </script>
@endsection





