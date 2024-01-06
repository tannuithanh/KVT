@extends('Layout.app')
@section('style')
    <style>
        .table-hover tbody tr:hover {
            cursor: pointer;
        }
        @keyframes scanner-animation {
            0% { top: 0; }
            100% { top: 100%; }
        }

        #scanner-line {
            position: absolute;
            width: 100%;
            border-top: 2px solid red; /* Đường line màu đỏ */
            top: 0;
            animation: scanner-animation 2s linear infinite;
        }



    </style>
@endsection
@section('title')
    Danh mục vật tư
@endsection

@section('content')
    <div class="pagetitle">
        <h1>Danh mục vật tư</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang chủ</a></li>
                <li class="breadcrumb-item">{{ $module }}</li>
                <li class="breadcrumb-item"><a href="{{ route('listBrand', ['module' => $module]) }}">Thương hiệu</a></li>
                <li class="breadcrumb-item"><a href="{{ route('listProject', [$segmentId, 'module' => $module]) }}">Dự án</a>
                </li>
                <li class="breadcrumb-item active">Danh sách vật tư</li>
            </ol>
        </nav>
    </div>

    <section class="section">

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="mt-2" style="font-size: 18px;font-weight: 600;color: #012970;">
                            <span style="font-size: 18px;font-weight: 600;color: #012970;">Thương hiệu: <span
                                    style="color: black">{{ $brandName }}</span> |
                                <span style="font-size: 18px;font-weight: 600;color: #012970;">Phân khúc: <span
                                        style="color: black">{{ $segmentName }}</span> |
                                    <span style="font-size: 18px;font-weight: 600;color: #012970;">Dự án: <span
                                            style="color: black">{{ $project->name }}</span> |
                                        <span style="font-size: 18px;font-weight: 600;color: #012970;">Tổng số vật tư: <span
                                                style="color: black">{{ $totalSupplies ?? 0 }}</span>
                        </h5>
                        <button type="button" class="btn btn-outline-primary ri-search-line" data-bs-toggle="modal"
                            data-bs-target="#Timkiemvattu"> Tìm kiếm</button>



                        <!-- Nội dung thông báo thành công -->
                        @if (session('success'))
                            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show mt-3"
                                role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @elseif(session('error'))
                            <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show mt-3"
                                role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-borderless table-bordered table-hover mt-2">

                                <thead>
                                    <tr>
                                    <tr>
                                        <th style="text-align: center" rowspan="2" scope="col"><input
                                                class="form-check-input" type="checkbox" id="main-checkbox"></th>
                                        <th style="text-align: center" rowspan="2" scope="col">Số đơn hàng</th>
                                        <th style="text-align: center" rowspan="2" scope="col">Tên vật tư</th>
                                        <th style="text-align: center" rowspan="2" scope="col">NCC</th>
                                        <th style="text-align: center" rowspan="2" scope="col">Nội dung</th>
                                        <th style="text-align: center" rowspan="2" scope="col">Mã số</th>
                                        <th style="text-align: center" rowspan="2" scope="col">Đơn vị tính</th>
                                        <th style="text-align: center" colspan="4" scope="col">Số lượng</th>
                                        <th style="text-align: center" rowspan="2" scope="col">Chi Phí</th>
                                        <th style="text-align: center" rowspan="2" scope="col">Barcode</th>
                                        <th style="text-align: center" rowspan="2" scope="col">Ghi chú</th>
                                        <th style="text-align: center" rowspan="2" scope="col">Thao tác</th>
                                    </tr>
                                    <tr>
                                        <!-- Các cột khác ở đây -->
                                        <th style="text-align: center" scope="col">Tổng</th>
                                        <th style="text-align: center" scope="col">Đã nhận</th>
                                        <th style="text-align: center" scope="col">Chưa nhận</th>
                                        <th style="text-align: center" scope="col">Đã xuất</th>
                                        <!-- Các cột khác ở đây -->
                                    </tr>

                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $stt = 1;
                                    @endphp
                                    @forelse ($supplies as $index => $supply)
                                        <tr>
                                            <td class="no-modal-trigger" style="text-align: center;vertical-align: middle;">
                                                <input class="form-check-input row-checkbox" type="checkbox"
                                                    data-id="{{ $supply->id }}">
                                            </td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $supply->sodonhang }}
                                            </td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $supply->tenvattu }}
                                            </td>
                                            <td style="text-align: center;vertical-align: middle;">
                                                {{ $supply->nhacungcap }}</td>
                                            <td style="text-align: center;vertical-align: middle;">
                                                {{ $supply->noidungphancum }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $supply->maso }}</td>
                                            <td style="text-align: center;vertical-align: middle;">
                                                {{ $supply->donvitinh }}</td>
                                            <td
                                                style="text-align: center;vertical-align: middle; color: black; font-weight: bold">
                                                {{ $supply->soluong }}</td>
                                            <td style="text-align: center;vertical-align: middle;"></td>
                                            <td style="text-align: center;vertical-align: middle;"></td>
                                            <td style="text-align: center;vertical-align: middle;"></td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $supply->chiphi }}
                                            </td>
                                            <td style="text-align: center;vertical-align: middle;">
                                                {!! DNS1D::getBarcodeHTML($supply->maso, 'C128', 1, 33) !!}
                                                <div style="vertical-align: middle;">P -
                                                    {{ $supply->maso }}</div>
                                            </td>

                                            <td style="text-align: center;vertical-align: middle;">{{ $supply->note }}
                                            </td>
                                            <td class="no-modal-trigger"
                                                style="text-align: center;vertical-align: middle;">

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="16" style="text-align: center;vertical-align: middle;">Không có
                                                dữ liệu</td>
                                        </tr>
                                    @endforelse
                                </tbody>

                            </table>
                            <div id="import-button-container" style="display: none; text-align: right; margin-top: 10px;">
                                <button type="button" class="btn btn-outline-primary ri-download-line"
                                    id="import-button">
                                    Nhập kho
                                </button>
                                <button type="button" id="print-barcode-button" class="btn btn-outline-primary bi bi-printer" id="import-button">
                                    In mã barcode
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- MODEL TÌM KIẾM --}}
        <div class="modal fade" id="Timkiemvattu" tabindex="-1" style="display: none;" aria-modal="false" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tìm kiếm vật tư</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="GET">
                            <input type="text" value="" name="project_id" hidden>
                            <div class="col-sm-12">
                                <div class="form-check d-flex align-items-center mt-2">
                                    <!-- Checkbox -->
                                    <div class="form-check col-sm-3">
                                        <input class="form-check-input" type="checkbox" id="tenvattu">
                                        <label class="form-check-label" for="tenvattu">
                                            Tên vật tư:
                                        </label>
                                    </div>

                                    @csrf
                                    <!-- Select menu -->
                                    <input type="text" class="form-control" id="tenvattuSuppelies" disabled>
                                </div>

                                <div class="form-check d-flex align-items-center mt-2">
                                    <!-- Checkbox -->
                                    <div class="form-check col-sm-3">
                                        <input class="form-check-input" type="checkbox" id="ngaynhan">
                                        <label class="form-check-label" for="ngaynhan">
                                            Ngày nhập kho:
                                        </label>
                                    </div>

                                    <input type="date" name="ngaynhan" id="ngaynhanSuppelies" class="form-control"
                                        disabled>
                                </div>
                                <div class="form-check d-flex align-items-center mt-2">
                                    <!-- Checkbox -->
                                    <div class="form-check col-sm-3">
                                        <input class="form-check-input" type="checkbox" id="nhacungcap">
                                        <label class="form-check-label" for="nhacungcap">
                                            Nhà cung cấp
                                        </label>
                                    </div>

                                    <select class="form-select ml-auto" id="nhacungcapSuppelies"
                                        name="nhacungcapSuppeliesSelect" aria-label="Default select example" required
                                        disabled>
                                        <option selected="">Chọn hạng mục</option>
                                        <option selected="">Nhà cung cấp 1</option>
                                        <option selected="">Nhà cung cấp 2</option>
                                    </select>
                                </div>

                                <div class="form-check d-flex align-items-center mt-2">
                                    <!-- Checkbox -->
                                    <div class="form-check col-sm-3">
                                        <input class="form-check-input" type="checkbox" id="maso">
                                        <label class="form-check-label" for="maso">
                                            Mã số:
                                        </label>
                                    </div>

                                    <input type="number" class="form-control" id="masoSuppelies" disabled>
                                </div>

                                <div class="form-check d-flex align-items-center mt-2">
                                    <!-- Checkbox -->
                                    <div class="form-check col-sm-3">
                                        <input class="form-check-input" type="checkbox" id="status">
                                        <label class="form-check-label" for="status">
                                            Trạng thái:
                                        </label>
                                    </div>

                                    <!-- Select menu -->
                                    <select class="form-select ml-auto" id="statusSuppeliesSelect" name="status"
                                        aria-label="Default select example" disabled>
                                        <option selected="">Chọn trạng thái</option>
                                        <option value="0">Đang chờ </option>
                                        <option value="1">Đã Nhập kho</option>
                                        <option value="-1">Đã lưu kho</option>
                                        <option value="-1">Đã xuất kho</option>
                                    </select>
                                </div>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary searchSuppelies">Tìm kiếm</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    {{-- LỊCH SỬ VẬT TƯ --}}
        <div class="modal fade" id="lichsuvattu" tabindex="-1" style="display: none;" aria-modal="false" role="dialog">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Lịch sử vật tư</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-borderless table-bordered mt-2">
                            <thead>
                                <tr>
                                <tr>
                                    <th style="text-align: center" rowspan="2" scope="col">Stt</th>
                                    <th style="text-align: center" rowspan="2" scope="col">Tên vật tư</th>
                                    <th style="text-align: center" rowspan="2" scope="col">Mã số</th>
                                    <th style="text-align: center" rowspan="2" scope="col">Số lượng</th>
                                    <th style="text-align: center" rowspan="2" scope="col">Ngày thực hiện</th>
                                    <th style="text-align: center" rowspan="2" scope="col">Trạng thái</th>
                                </tr>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

    {{-- XÁC NHẬN VẬT TƯ MUỐN XUẤT --}}
      <div class="modal fade" id="verticalycentered" tabindex="-1" style="display: none;" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-lg">
              <div class="modal-content">
                  <div class="modal-header">
                      <h5 class="modal-title">Danh sách in mã barcode</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;vertical-align: middle;">Stt</th>
                                        <th style="text-align: center;vertical-align: middle;">Tên</th>
                                        <th style="text-align: center;vertical-align: middle;">Mã số</th>
                                        <th style="text-align: center;vertical-align: middle;">Barcode</th>
                                    </tr>
                                </thead>
                                <tbody id="barcodeTableBody">
                                   
                                </tbody>
                            </table>
                        </div>
                    </div>                    
                  </div>
                  <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                      <button type="button" class="btn btn-primary bi bi-printer In-Barcode"> In mã</button>
                  </div>
              </div>
          </div>
      </div>
    {{-- MODAL QUÉT BARCODE --}}
    <div class="modal fade" id="fullscreenModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Quét Barcode và Nhập Vật Tư</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <!-- Bảng Vật Tư Đã Chọn -->
                        <div class="col-12 col-lg-8">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tên Vật Tư</th>
                                        <th>Mã Số</th>
                                        <th>Số Lượng Nhập</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Các dòng vật tư được thêm vào đây -->
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Camera Quét Barcode -->
                        <div class="col-12 col-lg-4 d-flex align-items-center justify-content-center">
                            <div id="barcode-scanner" class="border" style="position: relative; height: 200px; width: 100%;">
                                <div id="scanner-line"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>
    
    
    
@endsection

@section('script')
    {{-- Tick chọn 1 nhiều vật tư cùng 1 lúc --}}
        <script>
            $(document).ready(function() {
                // Khi click vào checkbox chính
                $('#main-checkbox').click(function() {
                    var isChecked = $(this).is(':checked');
                    $('.row-checkbox').prop('checked', isChecked);
                });

                // Mảng lưu trữ các ID được chọn
                var selectedIds = [];

                // Khi click vào mỗi checkbox dòng
                $('.row-checkbox').click(function() {
                    var rowId = $(this).data('id');
                    if ($(this).is(':checked')) {
                        // Thêm ID vào mảng nếu được chọn
                        selectedIds.push(rowId);
                    } else {
                        // Xóa ID khỏi mảng nếu không được chọn
                        selectedIds = selectedIds.filter(id => id !== rowId);
                    }
                });
            });
        </script>
    {{-- HIỂN THỊ NÚT NHẬP KHO --}}
        <script>
            $(document).ready(function() {
                $('#main-checkbox').click(function() {
                    var isChecked = $(this).is(':checked');
                    $('.row-checkbox').prop('checked', isChecked);
                    toggleButtons();
                });

                $('.row-checkbox').click(function() {
                    toggleButtons();
                });

                function toggleButtons() {
                    // Kiểm tra nếu có bất kỳ checkbox nào được chọn
                    var anyChecked = $('.row-checkbox:checked').length > 0;
                    $('#import-button-container').toggle(anyChecked);
                    $('#print-barcode-button-container').toggle(
                    anyChecked); // Thêm dòng này nếu bạn có một container riêng cho nút in mã barcode
                }
            });
        </script>
    {{-- COMFIRM DANH SÁCH IN BARCODE --}}
        <script>
        $(document).ready(function() {
                $('#print-barcode-button').click(function() {
                    var selectedItems = $('.row-checkbox:checked').closest('tr');
                    $('#barcodeTableBody').empty(); // Xóa nội dung hiện tại của bảng trong modal

                    var stt = 1; // Biến đếm số thứ tự
                    selectedItems.each(function() {
                        var name = $(this).find('td:eq(2)').text(); // Lấy tên
                        var code = $(this).find('td:eq(5)').text(); // Lấy mã số
                        var barcodeHtml = $(this).find('td:eq(12)').html(); // Lấy HTML của barcode

                        var newRow = '<tr><td style="text-align: center; vertical-align: middle;">' 
                            + stt + '</td><td style="text-align: center; vertical-align: middle;">' 
                            + name + '</td><td style="text-align: center; vertical-align: middle;">' 
                            + code + '</td><td style="vertical-align: middle;">' 
                            + barcodeHtml + '</td></tr>';
                        $('#barcodeTableBody').append(newRow);

                        stt++; // Tăng số thứ tự
                    });

                    $('#verticalycentered').modal('show'); // Hiển thị modal
                });
            });
        </script>  
    {{-- IN MÃ BARCODE --}}  
        <script>
            $(document).ready(function() {
                $('.In-Barcode').click(function() {
                    var printContents = document.getElementById('barcodeTableBody').innerHTML;
                    var originalContents = document.body.innerHTML;

                    document.body.innerHTML = printContents; // Thay thế nội dung trang hiện tại bằng nội dung muốn in
                    window.print(); // Gọi hộp thoại in

                    document.body.innerHTML = originalContents; // Khôi phục nội dung trang ban đầu
                });
            });
        </script>
    {{-- QUÉT MÃ BARCODE --}}
        <script>
            $('#import-button').click(function() {
                $('#fullscreenModal').modal('show'); // Hiển thị modal
            });
            $(document).ready(function() {
                $('#fullscreenModal').on('shown.bs.modal', function() {
                        Quagga.init({
                            inputStream: {
                                name: "Live",
                                type: "LiveStream",
                                target: document.querySelector('#barcode-scanner'), // Pass the element to render video
                                constraints: {
                                    width: 480,
                                    height: 320,
                                    facingMode: "environment" // Use the rear camera
                                },
                            },
                            decoder: {
                                readers: ["code_128_reader"] // You can add more readers here
                            },
                        }, function(err) {
                            if (err) {
                                console.log(err);
                                return;
                            }
                            console.log("Initialization finished. Ready to start");
                            Quagga.start();
                        });

                        Quagga.onDetected(function(data) {
                            var barcode = data.codeResult.code;
                            console.log("Barcode detected: ", barcode);
                            
                            // Gửi mã barcode đến máy chủ hoặc kiểm tra dữ liệu của vật tư
                            // Ví dụ: Gửi yêu cầu AJAX
                            $.ajax({
                                url: 'your-server-endpoint',
                                method: 'POST',
                                data: { barcode: barcode },
                                success: function(response) {
                                    // Xử lý phản hồi từ máy chủ
                                    // Ví dụ: Hiển thị thông tin vật tư
                                    console.log(response);
                                }
                            });

                            Quagga.stop(); // Dừng camera
                            $('#fullscreenModal').modal('hide'); // Ẩn modal
                        });

                    });
                });
        </script>
@endsection
