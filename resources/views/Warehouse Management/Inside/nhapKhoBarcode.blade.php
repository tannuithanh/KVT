@extends('Layout.app')
@section('style')
<style>

    #camera-stream {
        width: 30%; /* Chiếm toàn bộ chiều rộng của khu vực hiển thị */
        height: 20%; /* Chiếm toàn bộ chiều cao của khu vực hiển thị */
        object-fit: cover; /* Đảm bảo video được bao phủ đều mà không làm méo hình ảnh */
    }

    #barcode-scanner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border: solid 5px rgba(255, 255, 255, 0.8);
        box-sizing: border-box;
        border-radius: 10px; /* Tùy chọn */
        /* Tạo một "hố" ở giữa overlay */
        box-shadow: 0 0 0 2000px rgba(0, 0, 0, 0.5);
    }


    #scanner-activator:hover {
      background-color: #0056b3; /* Đổi màu nút khi hover */
    }

    /* Icon cho nút bấm, bạn có thể sử dụng font icon hoặc hình ảnh */
    #scanner-activator::before {
      content: '\25b6'; /* Dùng mã unicode cho một icon tam giác */
      display: block;
    }
</style>
@endsection
@section('content')
<div class="modal fade show" id="fullscreenModal" tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">NHẬP KHO</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-borderless table-bordered mt-2">
                        <thead>
                            <tr>
                                <th style="text-align: center" scope="col">Stt</th>
                                <th style="text-align: center" scope="col">Tên vật tư</th>
                                <th style="text-align: center" scope="col">Mã số</th>
                                <th style="text-align: center" scope="col">Số lượng</th>
                                <th style="text-align: center" scope="col">Số lượng nhập kho</th>
                                <th style="text-align: center" scope="col">Tình trạng</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $stt = 1;
                            @endphp
                                @foreach ($supplies as $index => $suppli)
                                    <tr data-maso="{{ $suppli->maso }}">
                                        <td style="text-align: center" scope="col">{{ $stt++ }}</td>
                                        <td style="text-align: center" scope="col">{{ $suppli->tenvattu }}</td>
                                        <td style="text-align: center" scope="col">{{ $suppli->maso  }}</td>
                                        <td style="text-align: center" scope="col">{{ $suppli->soluong }}</td>
                                        <td style="text-align: center" class="quantity" scope="col">-</td>
                                        <td style="text-align: center" scope="col">-</td>
                                    </tr>
                                @endforeach
                        </tbody>
                    </table>
                    <button id="scan-button" class="btn btn-outline-primary">Quét Mã</button>
                            <div id="barcode-scanner" class="fullscreen-scanner">
                                <video id="camera-stream" autoplay></video>
                            </div>
                </div>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở về</button>
          <button type="button" class="btn btn-primary">Lưu kho</button>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js"></script>
    <script type="text/javascript">
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
                        readers: ["code_128_reader", "upc_reader", "upc_e_reader", "code_39_reader"]
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

                let scannedBarcodes = []; // Danh sách các mã vạch đã quét

                Quagga.onDetected(function (data) {
                    var code = data.codeResult.code;
                    if (!scannedBarcodes.includes(code)) { // Kiểm tra xem mã vạch đã tồn tại trong danh sách chưa
                        scannedBarcodes.push(code); // Thêm mã vạch vào danh sách
                        var matchedRow = document.querySelector('tr[data-maso="' + code + '"]');
                        if (matchedRow) {
                            Swal.fire({
                                title: 'Nhập số lượng',
                                input: 'number',
                                inputLabel: 'Số lượng:',
                                inputAttributes: {
                                    autocapitalize: 'off'
                                },
                                showCancelButton: true,
                                confirmButtonText: 'Xác nhận',
                                cancelButtonText: 'Hủy',
                                showLoaderOnConfirm: true,
                                preConfirm: (quantity) => {
                                    // Xử lý khi người dùng xác nhận nhập số lượng
                                    matchedRow.querySelector('.quantity').innerText = quantity; // Cập nhật số lượng trong bảng
                                    return quantity;
                                }
                            });
                        } else {
                            console.log('Mã không khớp với bất kỳ sản phẩm nào.');
                        }
                    } else {
                        console.log('Mã vạch đã được quét trước đó.');
                    }
                });

            });
        });
    </script>

@endsection
