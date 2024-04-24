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
                    <table class="table table-borderless table-bordered mt-2 nhapkhokiemtra">
                        <thead>
                            <tr>
                                <th style="text-align: center" scope="col">Stt</th>
                                <th style="text-align: center" scope="col">Tên vật tư</th>
                                <th style="text-align: center" scope="col">Mã số</th>
                                <th style="text-align: center" scope="col">Số lượng nhập kho</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($supplies as $index => $supply)
                                <tr data-maso="{{ $supply['maso'] }}"
                                    data-projectId="{{ optional(optional(optional($supply['order'])['catalog'])['project'])['id'] }}"
                                    data-id="{{ $supply['id'] }}"
                                    data-tenvattu="{{ $supply['tenvattu'] }}">
                                    <td style="text-align: center">{{ $index + 1 }}</td>
                                    <td style="text-align: center">{{ $supply['tenvattu'] }}</td>
                                    <td style="text-align: center">{{ $supply['maso'] }}</td>
                                    <td style="text-align: center">-</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <button id="scan-button" class="btn btn-outline-primary bi bi-upc-scan"> Quét Mã</button>
                    <button id="nhapthucong" class="btn btn-outline-primary"><i class="bi bi-hand-index"></i> Nhập thủ công</button>
                            <div id="barcode-scanner" class="fullscreen-scanner">
                                <video id="camera-stream" autoplay></video>
                            </div>

                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="troveButton">Trở về</button>
          <button type="button" class="btn btn-primary" id="NhapKho">Lưu kho</button>
        </div>
      </div>
    </div>
  </div>
{{-- NHẬP SỐ LƯỢNG VẬT TƯ --}}
    <div class="modal fade" id="nhapsoluongvattu" tabindex="-1" style="display: none;background-color: #000000bb; " aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">...</h5>
                <input type="number" class="form-control" id="idVatTu" style="display: none">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="quantityInput">Số lượng nhập kho:</label>
                        <input type="number" class="form-control" id="quantityInput" min="1" value="1">
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở lại</button>
                <button type="button" class="btn btn-primary" id="luuNhapKho">Lưu</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/quagga.min.js') }}"></script>
    <script>
        document.getElementById("troveButton").addEventListener("click", function() {
            // Lấy projectId từ phần tử đầu tiên của danh sách
            var projectId = $('.nhapkhokiemtra tbody tr').first().data('projectid');
            // Tạo URL đầy đủ cho trang listNhapKho với projectId và module
            var routeURL = "{{ route('listNhapKho', ['project' => ':projectId', 'module' => 'Nhập kho']) }}";
            routeURL = routeURL.replace(':projectId', projectId);
            // Điều hướng trang đến route listNhapKho với projectId và module đã được lấy
            window.location.href = routeURL;
        });
    </script>
    <script type="text/javascript">
         var scannedBarcodes = [];

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

                    Quagga.onDetected(function (data) {
                        var code = data.codeResult.code;
                        if (scannedBarcodes.includes(code)) {
                            console.log('Mã này đã được quét và xử lý.');
                            return; // Nếu mã đã tồn tại, không làm gì cả
                        }
                    // Tiếp tục xử lý nếu mã chưa được quét
                    var matchedRow = document.querySelector('tr[data-maso="' + code + '"]');
                    if (matchedRow) {
                        var supplyId = matchedRow.dataset.id;
                        var tenvattu = matchedRow.dataset.tenvattu;

                        // Cập nhật modal trước khi hiển thị
                        var modal = $('#nhapsoluongvattu');
                        modal.find('.modal-title').text(`Nhập số lượng cho ${tenvattu}`);
                        modal.find('#idVatTu').val(supplyId); // Cập nhật giá trị cho input idVatTu

                        // Hiển thị modal
                        modal.modal('show');
                    } else {
                        console.log('Mã không khớp với bất kỳ sản phẩm nào.');
                    }
                });
            });
        });

    </script>
{{-- NHẬP INPUT SỐ LƯỢNG NHẬP KHO --}}
    <script>
        $(document).ready(function() {
                $('#luuNhapKho').click(function() {
                    var supplyId = $('#idVatTu').val(); // Lấy ID vật tư từ input ẩn
                    var quantity = $('#quantityInput').val(); // Lấy số lượng nhập từ input số lượng

                    // Hiển thị popup xác nhận trước khi lưu
                    Swal.fire({
                        title: 'Bạn có chắc chắn không?',
                        text: "Thao tác này không thể hoàn tác!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Có, tôi chắc chắn!',
                        cancelButtonText: 'Không, hủy bỏ!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route("KiemTraSoluongTruocKhiNhapKho") }}',
                                type: 'POST',
                                data: {
                                    supplyId: supplyId,
                                    quantity: quantity,
                                    _token: $('meta[name="csrf-token"]').attr('content') // CSRF token cho Laravel
                                },
                                success: function(response) {
                                    var matchedRow = $('tr[data-id="' + supplyId + '"]');
                                        if (matchedRow.length) {
                                            matchedRow.find('td:nth-child(4)').text(quantity);
                                            $('#nhapsoluongvattu').modal('hide');
                                        }
                                    Swal.fire(
                                        'Lưu thành công!',
                                        response.message,
                                        'success'
                                    );
                                },
                                error: function(xhr) {
                                    // Đoạn mã xử lý khi có lỗi
                                    var errorMessage = xhr.responseJSON ? xhr.responseJSON.message : 'Có lỗi xảy ra, vui lòng thử lại.';
                                    Swal.fire(
                                        'Lỗi!',
                                        errorMessage,
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });
            });


    </script>


{{-- NHẬP SỐ LƯỢNG VẬT TƯ BẰNG TAY --}}
    <script>
        $(document).ready(function() {
            $('#nhapthucong').click(function() {
                var $this = $(this); // Lưu trữ tham chiếu đến nút được nhấn

                // Kiểm tra trạng thái của nút
                if ($this.html().includes("Nhập thủ công")) {
                    // Nếu nút đang ở trạng thái "Nhập thủ công"
                    var supplyIds = $('.nhapkhokiemtra tbody tr').map(function() {
                        return $(this).data('id');
                    }).get();

                    $.ajax({
                        url: "{{route('KiemTraSoluongTruocKhiNhapKhoV2')}}", // Sửa URL cho phù hợp
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ supplyIds: supplyIds }),
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            // Cập nhật trạng thái của nút sang "Lưu"
                            $this.html('<i class="bi bi-save"></i> Lưu');
                            // Hiển thị input với ràng buộc
                            $('.nhapkhokiemtra tbody tr').each(function() {
                                var supplyId = $(this).data('id');
                                var maxQuantity = response[supplyId]; // Giả sử server trả về {supplyId: maxQuantity}
                                var currentValue = $(this).find('td').eq(3).text();
                                $(this).find('td').eq(3).html('<input type="number" class="form-control" value="' + currentValue + '" max="' + maxQuantity + '"/>');
                            });
                        },
                        error: function(xhr) {
                            alert('Có lỗi xảy ra khi kiểm tra số lượng tối đa có thể nhập.');
                        }
                    });
                } else {
                    // Nếu nút đang ở trạng thái "Lưu"
                    var overLimit = []; // Dùng để lưu các dòng nhập quá giới hạn

                    $('.nhapkhokiemtra tbody tr input[type="number"]').each(function() {
                        var max = parseInt($(this).attr('max'));
                        var value = parseInt($(this).val());
                        var tenvattu = $(this).closest('tr').data('tenvattu');

                        if (value > max) {
                            overLimit.push(`${tenvattu} đã nhập quá số lượng (${value}/${max})`);
                        }
                    });

                if (overLimit.length > 0) {
                        // Hiển thị thông báo lỗi với SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi nhập số lượng',
                            html: overLimit.join('<br>'),
                        });
                    } else {
                        // Loại bỏ các input và chuyển giá trị về td
                        $('.nhapkhokiemtra tbody tr').each(function() {
                            var input = $(this).find('input[type="number"]');
                            var value = input.val();
                            input.parent().html(value);
                        });

                        // Chuyển nút về trạng thái "Nhập thủ công"
                        $this.html('<i class="bi bi-hand-index"></i> Nhập thủ công');
                        // Xóa class 'btn-success' và thêm 'btn-outline-primary' nếu đã thêm vào trước đó
                        $this.addClass('btn-outline-primary').removeClass('btn-success');
                        }
                }
            });
        });
    </script>
{{-- LƯU THÔNG TIN KHI NHẬP KHO --}}
    <script>
        $(document).ready(function() {
            $('#NhapKho').click(function() {
                var dataToSend = [];
                $('.nhapkhokiemtra tbody tr').each(function() {
                    var id = $(this).data('id');
                    var quantity = $(this).find('td:last-child').text();
                    var projectId = $(this).data('projectid');

                    if (!quantity || isNaN(quantity)) {
                        Swal.fire('Lỗi', 'Bạn phải nhập số lượng vật tư cho tất cả các mục.', 'error');
                        return false; // Dừng vòng lặp .each()
                    }

                    dataToSend.push({id: id, quantity: quantity, project_id: projectId});
                });

                if (dataToSend.length > 0) {
                    $.ajax({
                        url: "{{ route('updateQuanlity') }}",
                        type: 'POST',
                        data: JSON.stringify({ supplies: dataToSend }), // Đảm bảo cấu trúc dữ liệu này phù hợp
                        contentType: 'application/json; charset=utf-8',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function(response) {
                            Swal.fire('Thành công', 'Dữ liệu đã được cập nhật thành công.', 'success')
                            .then(() => {
                                // Điều hướng đến trang listNhapKho với tham số module='Nhập kho'
                                var projectId = $('.nhapkhokiemtra tbody tr').first().data('projectid');
                                var routeURL = "{{ route('listNhapKho', ['project' => ':projectId']) }}";
                                routeURL = routeURL.replace(':projectId', projectId);
                                window.location.href = routeURL + "?module=Nhập kho";
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            Swal.fire('Lỗi', 'Có lỗi xảy ra khi cập nhật dữ liệu.', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endsection
