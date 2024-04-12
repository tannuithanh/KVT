@extends('Layout.app')
@section('style')
<link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" />
<style>
    .filter-box {
        border: 1px solid #173e864f; /* Màu border, có thể điều chỉnh */
        padding: 11px;
        margin-bottom: 20px; /* Khoảng cách với nội dung tiếp theo */
        border-radius: 5px; /* Bo góc cho khung */
    }
    .viewport {
        width: 100%;
        height: 400px;
        position: relative;
        border: 1px solid #ccc;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.5);
    }
</style>
@endsection
@section('title')
    Kiểm tra chất lượng
@endsection

@section('content')
<div class="pagetitle">
    <h1>Kiểm tra chất lượng</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Trang chủ</a></li>
            <li class="breadcrumb-item">Kiểm tra chất lượng</li>

        </ol>
    </nav>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mt-2" style="font-size: 18px;font-weight: 600;color: #012970;">
                    </h5>
                    <div class="filter-box mt-3">
                        <div class="row">
                            <div class="col-6 col-md-2" >
                                <input class="form-select ngaykiemtra"  type="date">
                            </div>
                            <div class="col-6 col-md-2" >
                                <select class="form-select donvitinhchitiet status" aria-label="Default select example" >
                                    <option value="1">Đã kiểm tra</option>
                                    <option value="0">Chưa kiểm tra</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <button type="submit" id="timkiemVatTuChiTiet" class="btn btn-primary" style="margin-left: -5px">Tìm kiếm</button>
                            </div>
                        </div>
                    </div>
                          <div class="table-responsive">
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
                                    @php
                                        $stt = 1;
                                    @endphp
                                    @forelse ($qualityChecks as $item)
                                    <tr data-id="{{ $item->id }}" data-maso="{{ $item->supply->maso }}" data-tenvattu="{{ $item->supply->tenvattu }}" data-status="{{$item->status}}">
                                            <td style="text-align: center">{{ $stt++ }}</td>
                                            <td style="text-align: center">{{ $item->supply->order->sodonhang ?? 'N/A' }}</td>
                                            <td style="text-align: center">{{ $item->supply->tenvattu }}</td>
                                            <td style="text-align: center">{{ $item->supply->maso }}</td>
                                            <td style="text-align: center">{{ $item->soluongnhapkho }}</td>
                                            <td style="text-align: center">{{ $item->soluongdatchatluong ?? '' }}</td>
                                            <td style="text-align: center; background-color: {{ $item->status == 0 ? 'yellow' : 'green' }}">{{ $item->status == 0 ? 'Chưa kiểm tra' : 'Đã kiểm tra' }}</td>
                                            <td style="text-align: center">{{ $item->ngaykiemtra ?? '' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" style="text-align: center" scope="col">Không có vật tư cần kiểm tra</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            <button id="scan-button" class="btn btn-outline-primary bi bi-upc-scan mt-2"> Quét Mã</button>
                            <div id="barcode-scanner" class="fullscreen-scanner" style="display:none;">
                                <video id="camera-stream" autoplay></video>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
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
                <div class="form-group">
                    <label for="quantityInput">Ghi chú:</label>
                    <textarea class="form-control" id="ghichuText" ></textarea>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở lại</button>
            <button type="button" class="btn btn-primary" id="luuKiemTraChatLuong">Lưu</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
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
                        <div class="form-group">
                            <label for="quantityInput">Ghi chú:</label>
                            <textarea class="form-control" id="ghichuText" ></textarea>
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
    {{-- LƯU KIỂM TRA CHẤT LƯỢNG --}}
        <script>
        $(document).ready(function() {
                $('#luuKiemTraChatLuong').click(function() {
                    var idQualityCheck = $('#idQualityCheck').val();
                    var quantityDat = $('#quantityDat').val();
                    var ghiChu = $('#ghichuText').val(); // Lấy giá trị từ textarea

                    $.ajax({
                        url: "{{ route('luuKiemTraChatLuong') }}",
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            idQualityCheck: idQualityCheck,
                            quantityDat: quantityDat,
                            ghiChu: ghiChu // Thêm dữ liệu ghi chú vào request
                        },
                        success: function(response) {
                            if(response.success) {
                                alert('Dữ liệu đã được lưu thành công.');
                                var row = $('tr[data-id="' + idQualityCheck + '"]');
                                row.find('td').eq(5).text(quantityDat); // Cập nhật số lượng đạt

                                // Cập nhật màu sắc dựa vào status trong phản hồi
                                if(response.status == 1) {
                                    row.find('td').eq(6).css('background-color', '#00FF00'); // Đổi màu nền của cột thứ 7 (index bắt đầu từ 0)
                                    row.find('td').eq(6).text('Đã kiểm tra'); // Cập nhật văn bản cho cột thứ 7 là "Đã kiểm tra"
                                    row.find('td').eq(7).text(response.ngaykiemtra);
                                } else {
                                    row.css('background-color', '#FFFF00'); // Đổi màu nền của cả hàng nếu chưa kiểm tra
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
    {{--TÌM KIẾM VẬT TƯ--}}
        <script>
            $(document).ready(function() {
                $('#timkiemVatTuChiTiet').click(function() {
                    // Lấy giá trị từ các input và select box
                    var ngayKiemTra = $('.ngaykiemtra').val();
                    var status = $('.status').val(); // Cập nhật selector nếu cần
                    $.ajax({
                        url: "{{ route('timKiemVatTuCheck') }}",
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
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
                                        // Định dạng ngày kiểm tra
                                        var ngaykiemtraFormatted = '';
                                        if (item.ngaykiemtra) {
                                            var date = new Date(item.ngaykiemtra);
                                            var day = ("0" + date.getDate()).slice(-2);
                                            var month = ("0" + (date.getMonth() + 1)).slice(-2);
                                            var year = date.getFullYear();
                                            ngaykiemtraFormatted = day + '/' + month + '/' + year;
                                        }

                                        tbodyContent += '<tr data-id="' + item.id + '" data-maso="' + item.supply.maso + '" data-tenvattu="' + item.supply.tenvattu + '" data-status="' + item.status + '">';
                                        tbodyContent += '<td style="text-align: center">' + (stt++) + '</td>';
                                        tbodyContent += '<td style="text-align: center">' + item.supply.order.sodonhang + '</td>';
                                        tbodyContent += '<td style="text-align: center">' + item.supply.tenvattu + '</td>';
                                        tbodyContent += '<td style="text-align: center">' + item.supply.maso + '</td>';
                                        tbodyContent += '<td style="text-align: center">' + item.soluongnhapkho + '</td>';
                                        tbodyContent += '<td style="text-align: center">' + (item.soluongdatchatluong || '') + '</td>';
                                        tbodyContent += '<td style="text-align: center; background-color:' + (item.status == 1 ? '#00FF00' : '#FFFF00') + '">' + (item.status == 1 ? 'Đã kiểm tra' : 'Chưa kiểm tra') + '</td>';
                                        tbodyContent += '<td style="text-align: center">' + ngaykiemtraFormatted + '</td>';
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
@endsection

