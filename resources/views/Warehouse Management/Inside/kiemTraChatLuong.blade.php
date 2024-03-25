<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">

        <title>Kiểm tra chất lượng</title>
        <meta content="" name="description">
        <meta content="" name="keywords">
        <link rel="icon" href="{{asset('favicon.ico')}}" type="image/x-icon"/>
        <link rel="shortcut icon" href="{{asset('favicon.ico')}}" type="image/x-icon"/>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Favicons -->
        <link href="{{asset('assets/img/logo1.png')}}" rel="icon">
        <link href="{{asset('assets/img/apple-touch-icon.png')}}" rel="apple-touch-icon">
        <link href="{{asset('assets/css/fontquantrong.css')}}"rel="stylesheet">

        <!-- Vendor CSS Files -->
        <link href="{{asset('assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/quill/quill.snow.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/quill/quill.bubble.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/remixicon/remixicon.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/simple-datatables/style.css')}}" rel="stylesheet">
        <link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
        <link href="{{asset('assets/css/TH.css')}}" rel="stylesheet">
        <link href="{{asset('assets/css/loadingPage.css')}}" rel="stylesheet">
        <link href="{{ asset('assets/css/sweetalert2.min.css') }}" rel="stylesheet">
        <link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" />
        <style>
            .sticky-header th {
                position: -webkit-sticky; /* For Safari */
                position: sticky;
                top: 0;
                background-color: #fff; /* Background color to make header visible over text */
                z-index: 2; /* Ensure the header is above other content */
            }
            .table-container {
                overflow-y: auto; /* Enable vertical scrolling */
                max-height: 400px; /* Set maximum height */
            }
        </style>
        <style>
            .select2-container--default .select2-selection--single,
            .select2-selection .select2-selection--single {
                border: 1px solid #ced4da;
                border-radius: 0.30rem;
                height: calc(2.25rem + 2px);
                line-height: 1.5;
            }

            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: calc(2.25rem + 2px);
            }

            .select2-container .select2-selection--single .select2-selection__rendered {
                padding-left: 0.75rem;
            }

            .select2-container--default .select2-selection--single .select2-selection__rendered {
                line-height: 2.25rem;
            }

            .select2-container--open .select2-dropdown {
                border-color: #ced4da;
                z-index: 9999;
            }
            .filter-box {
                    border: 1px solid #173e864f; /* Màu border, có thể điều chỉnh */
                    padding: 11px;
                    margin-bottom: 20px; /* Khoảng cách với nội dung tiếp theo */
                    border-radius: 5px; /* Bo góc cho khung */
                }
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
    </head>
<body>
      <main id="main" class="main content">
        <div class="modal fade show" id="fullscreenModal" tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog modal-fullscreen">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Kiểm tra chất lượng</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <h5>Danh sách vật tư cần kiểm tra </h5>
                  <h5>Dự án: {{$project->name}}</h5>
                    <div class="filter-box mt-3">
                        <div class="row">
                            <div class="col-6 col-md-2">
                                <select class="form-select masochitiet" aria-label="Default select example">
                                    <option value="">Mã số</option>
                                    @foreach ($supplies as $supply)
                                        <option value="{{ $supply->id }}">{{ $supply->maso }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <select class="form-select status" aria-label="Default select example">
                                    <option value="" selected="">Tình trạng</option>
                                    <option value="0">Chưa kiểm tra</option>
                                    <option value="1" >Đã kiểm tra</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2" >
                                <input class="form-select status ngaykiemtra"  type="date">
                            </div>
                            <div class="col-12 col-md-2">
                                <a type="submit" id="timkiemVatTu" class="btn btn-primary btn-block">Tìm kiếm</a>
                            </div>
                        </div>
                    </div>
                  <div class="col-md-12">

                    <div class="table-responsive" style="max-height: 500px;">
                        <table class="table table-borderless table-bordered sticky-header nhapkhokiemtra">
                            <thead>
                                <tr>
                                    <th style="text-align: center" scope="col">Stt</th>
                                    <th style="text-align: center" scope="col">Đơn hàng</th>
                                    <th style="text-align: center" scope="col">Tên vật tư</th>
                                    <th style="text-align: center" scope="col">Mã số</th>
                                    <th style="text-align: center" scope="col">Số lượng nhập</th>
                                    <th style="text-align: center" scope="col">Số lượng đạt</th>
                                    <th style="text-align: center" scope="col">Tình trạng</th>
                                    <th style="text-align: center" scope="col">Ngày kiểm tra</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $stt = 1; @endphp
                                @foreach ($project->orders as $order)
                                    @foreach ($order->supplies as $supply)
                                        @foreach ($supply->qualityChecks as $qualityCheck)
                                            <tr data-id="{{ $qualityCheck->id }}" data-maso="{{ $supply->maso }}" data-tenvattu="{{ $supply->tenvattu }}" data-status="{{$qualityCheck->status}}">
                                                <td style="text-align: center">{{ $stt++ }}</td>
                                                <td style="text-align: center">{{ $order->sodonhang }}</td>
                                                <td style="text-align: center">{{ $supply->tenvattu }}</td>
                                                <td style="text-align: center">{{ $supply->maso }}</td>
                                                <td style="text-align: center">{{ $qualityCheck->soluongnhapkho }}</td>
                                                <td style="text-align: center">{{ $qualityCheck->soluongdatchatluong }}</td>
                                                <td style="text-align: center; background-color:
                                                    @if($qualityCheck->status == 1) #00FF00 @elseif($qualityCheck->status == 0) #FFFF00 @endif">
                                                    {{ $qualityCheck->status == 1 ? 'Đã kiểm tra' : 'Chưa kiểm tra' }}
                                                </td>
                                                <td style="text-align: center">{{ $qualityCheck->ngaykiemtra }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button id="scan-button" class="btn btn-outline-primary bi bi-upc-scan mt-2">Quét Mã</button>
                    <div id="barcode-scanner" class="fullscreen-scanner" style="display:none;">
                        <video id="camera-stream" autoplay></video>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="troveButton">Trở về</button>
                </div>
              </div>
            </div>
          </div>
{{-- NHẬP SỐ LƯỢNG ĐÃ KIỂM TRA --}}
    <div class="modal fade" id="modalkiemtra" tabindex="-1" style="display: none;background-color: #000000bb" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">...</h5>
                <input type="number" class="form-control" id="idQualityCheck" style="display: none">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="quantityInput">Nhập số lượng đã đạt chất lượng:</label>
                        <input type="number" class="form-control" id="quantityDat" min="1" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở lại</button>
                <button type="button" class="btn btn-primary" id="luuKiemTraChatLuong">Lưu</button>
                </div>
            </div>
        </div>
    </div>
      </main><!-- End #main -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/quagga.min.js') }}"></script>
    <script src="{{asset('assets/js/select2.min.js')}}"></script>
{{-- QUÉT CAMERA --}}
    <script>
        $(document).ready(function() {
            $('#scan-button').click(function() {
                $('#barcode-scanner').show();

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
                }, function(err) {
                    if (err) {
                        console.log(err);
                        alert("Không khởi tạo được QuaggaJS: " + err);
                        return;
                    }
                    Quagga.start();
                });

                Quagga.onDetected(function(data) {
                    var code = data.codeResult.code;
                    var matchedRow = $('.nhapkhokiemtra tbody tr').filter(function() {
                        return $(this).data('maso') === code && $(this).data('status') == 0;
                    }).first();

                    if (matchedRow.length > 0) {
                        var id = matchedRow.data('id');
                        var tenvattu = matchedRow.data('tenvattu');
                        var maso = matchedRow.data('maso');
                        $('#modalkiemtra .modal-title').text(tenvattu);
                        $('#idQualityCheck').val(id);

                        // Hiển thị modal
                        $('#modalkiemtra').addClass('show').css({'display': 'block', 'background-color': '#000000bb'});
                        $('.modal-backdrop').remove();
                        $(document.body).append('<div class="modal-backdrop fade show"></div>');
                        $(document.body).addClass('modal-open');
                    } else {
                        if (!window.alertShown) {
                            alert('Không có vật tư nào có mã số "' + maso + '" trong danh sách cần kiểm tra chất lượng.');
                            window.alertShown = true; // Đánh dấu alert đã được hiển thị
                        }

                    }
                });
            });

            // Khi đóng modal, dừng Quagga và ẩn modal
            $('#modalkiemtra .btn-close, #modalkiemtra .btn-secondary').click(function() {
                Quagga.stop(); // Dừng Quagga
                $('#modalkiemtra').removeClass('show').css('display', 'none');
                $('.modal-backdrop').remove();
                $(document.body).removeClass('modal-open');
            });
        });
    </script>
{{-- TRỞ VỀ --}}
    <script>
        document.getElementById("troveButton").addEventListener("click", function() {
            // Lấy projectId từ phần tử đầu tiên của danh sách
            var projectId = {{$id}};
            // Tạo URL đầy đủ cho trang listNhapKho với projectId và module
            var routeURL = "{{ route('listNhapKho', ['project' => ':projectId', 'module' => 'Nhập kho']) }}";
            routeURL = routeURL.replace(':projectId', projectId);
            // Điều hướng trang đến route listNhapKho với projectId và module đã được lấy
            window.location.href = routeURL;
        });
    </script>
{{-- LƯU KIỂM TRA CHẤT LƯỢNG --}}
    <script>
        $(document).ready(function() {
            $('#luuKiemTraChatLuong').click(function() {
                var idQualityCheck = $('#idQualityCheck').val();
                var quantityDat = $('#quantityDat').val();
                $.ajax({
                    url: "{{ route('luuKiemTraChatLuong') }}",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        idQualityCheck: idQualityCheck,
                        quantityDat: quantityDat
                    },
                    success: function(response) {
                        console.log(response);
                        if(response.success) {
                            alert('Dữ liệu đã được lưu thành công.');
                            var row = $('tr[data-id="' + idQualityCheck + '"]');
                            row.find('td').eq(5).text(quantityDat); // Cập nhật số lượng đạt

                            // Cập nhật màu sắc dựa vào status trong phản hồi
                            if(response.status == 1) {
                                row.find('td').eq(6).css('background-color', '#00FF00'); // Đổi màu nền của cột thứ 7 (index bắt đầu từ 0)
                                row.find('td').eq(6).text('Đã kiểm tra'); // Cập nhật văn bản cho cột thứ 7 là "Đã kiểm tra"
                            } else {
                                row.css('background-color', '#FFFF00'); // Đổi màu nền của cả hàng nếu chưa kiểm tra
                                // Nếu bạn muốn cập nhật văn bản cho cột thứ 7 ở trạng thái chưa kiểm tra, hãy thêm dòng dưới đây
                                row.find('td').eq(6).text('Chưa kiểm tra');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Có lỗi xảy ra khi lưu dữ liệu: ' + error);
                    }
                });
            });
        });
    </script>
{{--SELECT 2--}}
    <script>
        $(document).ready(function() {
            $('.masochitiet').select2({
                placeholder: "Chọn mã số",
                allowClear: true
            });
        });
    </script>

{{--TÌM KIẾM VẬT TƯ--}}
    <script>
        $(document).ready(function() {
            $('#timkiemVatTu').click(function() {
                // Lấy giá trị từ các select box
                var idVatTu = $('.masochitiet').val();
                var status = $('.status').val();
                var ngayKiemTra = $('.ngaykiemtra').val();
                $.ajax({
                    url: "{{ route('timKiemVatTuCheck') }}",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        idVatTu: idVatTu,
                        status: status,
                        ngayKiemTra: ngayKiemTra
                    },
                    success: function(response) {
                        console.log(response)
                            // Kiểm tra nếu yêu cầu thành công và có dữ liệu trả về
                            if(response.success && response.data.length > 0) {
                                var tbodyContent = ''; // Chuỗi HTML cho nội dung mới của tbody
                                var stt = 1; // Số thứ tự

                                response.data.forEach(function(item) {

                                    tbodyContent += '<tr data-id="' + item.id + '" data-maso="' + item.supply.maso + '" data-tenvattu="' + item.supply.tenvattu + '" data-status="' + item.status + '">';
                                    tbodyContent += '<td style="text-align: center">' + (stt++) + '</td>';
                                    tbodyContent += '<td style="text-align: center">' + item.supply.order.sodonhang + '</td>';
                                    tbodyContent += '<td style="text-align: center">' + item.supply.tenvattu + '</td>';
                                    tbodyContent += '<td style="text-align: center">' + item.supply.maso + '</td>';
                                    tbodyContent += '<td style="text-align: center">' + item.soluongnhapkho + '</td>';
                                    tbodyContent += '<td style="text-align: center">' + (item.soluongdatchatluong || '') + '</td>';
                                    tbodyContent += '<td style="text-align: center; background-color:' + (item.status == 1 ? '#00FF00' : '#FFFF00') + '">' + (item.status == 1 ? 'Đã kiểm tra' : 'Chưa kiểm tra') + '</td>';
                                    tbodyContent += '<td style="text-align: center">' + (item.ngaykiemtra || '') + '</td>';
                                    tbodyContent += '</tr>';
                                });

                                // Cập nhật nội dung của tbody trong bảng
                                $('.nhapkhokiemtra tbody').html(tbodyContent);
                            } else {
                                // Nếu không có dữ liệu, hiển thị thông báo hoặc làm sạch tbody
                                $('.nhapkhokiemtra tbody').html('<tr><td colspan="8" style="text-align: center">Không có dữ liệu</td></tr>');
                            }
                        },
                    error: function(xhr, status, error) {
                        // Xử lý lỗi
                        console.error(error);
                    }
                });
            });
        });
    </script>

</body>
</html>
