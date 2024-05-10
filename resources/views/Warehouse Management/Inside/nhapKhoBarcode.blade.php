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
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/toastify.min.css')}}">
@endsection
@section('content')
<div class="pagetitle">
    <h1>Quét barcode nhập kho</h1>
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
    <script src="{{ asset('assets/js/toastify.min.js') }}" ></script>
{{-- QUÉT BARCODE KHI NHẬP KHO--}}
    <script>
        var supplies = @json($supplies);
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

                var scannedBarcodes = []; // Mảng để theo dõi các mã đã quét
                Quagga.onDetected(function (data) {
                    var code = data.codeResult.code;
                    if (scannedBarcodes.includes(code)) {
                        console.log('Mã này đã được quét và xử lý.');
                        return; // Nếu mã đã tồn tại, không làm gì cả
                    }

                    var matchedSupply = supplies.find(supply => supply.maso === code);
                    if (matchedSupply) {
                        scannedBarcodes.push(code); // Thêm mã vào mảng các mã đã quét

                        var cardHtml = `
                            <div class="card mb-3" style="border-radius: 15px; background-color: #007bff; color: white;">
                                <div class="card-header" style="background-color: #0056b3;color: white; border-top-left-radius: 15px; border-top-right-radius: 15px;">
                                    Đơn hàng: ${matchedSupply.order.sodonhang}
                                </div>
                                <div class="card-body" style="background-color: #007bff;">
                                    <h5 class="card-title">${matchedSupply.tenvattu}</h5>
                                    <p class="card-text">Mã số: ${matchedSupply.maso}</p>
                                    <p class="card-text">Số lượng nhập kho: ${matchedSupply.daNhan}</p>
                                </div>
                            </div>`;
                        document.querySelector('#Danhmucvattuxuat').innerHTML += cardHtml;
                        $.ajax({
                            url: "{{ route('updateQuanlity') }}",
                            type: "POST",
                            data: {
                                supplies: [
                                    {
                                        id: matchedSupply.id,
                                        quantity: matchedSupply.daNhan
                                    }
                                ]
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                Toastify({
                                    text: `Nhập kho thành công: ${matchedSupply.tenvattu}`,
                                    duration: 3000,
                                    close: true,
                                    gravity: "top", // `top` or `bottom`
                                    position: "right", // `left`, `center` or `right`
                                    backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
                                    className: "info",
                                }).showToast();
                            },
                            error: function(xhr, status, error) {
                                console.error('Có lỗi xảy ra: ', error);
                                Toastify({
                                    text: "Có lỗi xảy ra khi cập nhật",
                                    duration: 3000,
                                    close: true,
                                    gravity: "top", // `top` or `bottom`
                                    position: "right", // `left`, `center` or `right`
                                    backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
                                    className: "error",
                                }).showToast();
                            }
                        });
                    }
                });
            });
        });
    </script>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            if (sessionStorage.getItem("visited")) {
                // Nếu đã có cờ "visited", kiểm tra nếu là reload thì thực hiện chuyển hướng
                if (sessionStorage.getItem("reload")) {
                    var supplies = @json($supplies);
                    if (supplies.length > 0 && supplies[0].order && supplies[0].order.catalog) {
                        var projectId = supplies[0].order.catalog.project_id;
                        var routeURL = "{{ route('listNhapKho', ['project' => ':projectId', 'module' => 'Nhập kho']) }}";
                        routeURL = routeURL.replace(':projectId', projectId);
                        window.location.href = routeURL;
                    }
                    sessionStorage.removeItem("reload"); // Xóa cờ reload sau khi chuyển hướng
                }
            } else {
                // Đặt cờ "visited" khi trang được tải lần đầu
                sessionStorage.setItem("visited", "true");
            }
        });

        window.addEventListener("beforeunload", function() {
            sessionStorage.setItem("reload", "true"); // Đặt cờ reload khi trang bắt đầu unload
        });
    </script>
@endsection





