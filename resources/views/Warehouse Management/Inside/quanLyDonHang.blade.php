@extends('Layout.app')
@section('style')
    <link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" />
    <style>
        .blink-warning {
                background-color: rgba(255, 255, 0, 0.329) !important
            }
        .table-hover tbody tr:hover {
            cursor: pointer;
        }
        @media print {
            .btn, .close, .koin {
                display: none; /* Ẩn các nút và div 'koin' khi in */
            }
        }
        .table-responsive th, .table-responsive td {
            padding: 8px; /* Điều chỉnh padding nếu cần */
        }
        .disabled-row {
            opacity: 0.5; /* Làm mờ dòng */
            pointer-events: none; /* Ngăn chặn sự kiện chuột */
        }
        .disabled-row .form-check-input {
            display: none; /* Ẩn checkbox */
        }
        @media (max-width: 768px) {
            .filter-box {
                flex-direction: column; /* Stack the items vertically on small screens */
                gap: 10px; /* Reduce the gap */
            }
            .filter-box > div,
            .filter-box > button {
                max-width: 100%; /* Full width on small screens */
                flex-grow: 1; /* Allow the children to fill the space */
            }
        }
        .filter-box {
                border: 1px solid #173e864f; /* Màu border, có thể điều chỉnh */
                padding: 11px;
                margin-bottom: 20px; /* Khoảng cách với nội dung tiếp theo */
                border-radius: 5px; /* Bo góc cho khung */
            }
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
    </style>
@endsection
@section('title')
    Quản lý đơn hàng
@endsection
@section('content')
<div class="pagetitle">
    <h1>Quản lý đơn hàng</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Trang chủ</a></li>
            <li class="breadcrumb-item active">Quản Lý Đơn Hàng</li>
        </ol>
    </nav>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mt-2" style="font-size: 18px;font-weight: 600;color: #012970;">
                        Danh sách đơn hàng
                    </h5>
                    <div  style=" display: flex; gap: 15px; align-items: center;">
                        <!-- Nhóm Select và Button Tìm Kiếm -->
                        <div style="display: flex; gap: 15px; align-items: center; border: 1px solid #ccc; border-radius: 8px; padding: 5px;">
                            <!-- Select Số Đơn Hàng -->
                            <div style="flex-grow: 1; max-width: 200px;">
                                <select class="form-select status" aria-label="Default select example">
                                    <option value="1">Dự án</option>
                                    <option value="0">Chưa kiểm tra</option>
                                </select>
                            </div>
                            <div style="flex-grow: 1; max-width: 200px;">
                                <select class="form-select status" aria-label="Default select example">
                                    <option value="1">Tìm số đơn hàng</option>
                                    <option value="0">Chưa kiểm tra</option>
                                </select>
                            </div>
                            <!-- Nút Tìm Kiếm -->
                            <button type="submit" id="timkiemVatTuChiTiet" class="btn btn-primary" style="flex-shrink: 0;">Tìm kiếm</button>
                        </div>
                        <!-- Button "Danh mục đang chờ" nằm cách biệt -->
                        <button type="button" class="btn btn-primary danhmucdangcho" style="flex-shrink: 0;">
                            Danh mục đang chờ <span class="badge bg-white text-primary">{{$countCatalogsWithoutOrders ?? '0'}}</span>
                        </button>

                    </div>

                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-hover vattuchitietdonhang">
                            <thead>
                                <tr>
                                    <th>Stt</th>
                                    <th>Số đơn hàng</th>
                                    <th>Chi phí</th>
                                    <th>Ghi chú</th>
                                    <th>File đính kèm</th>
                                    <th>Tình trạng</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $index => $order)
                                    <tr data-order-id="{{$order->id}}">
                                        <td style="text-align: center; vertical-align: middle">{{ $index + 1 }}</td>
                                        <td style="text-align: center; vertical-align: middle">{{ $order->sodonhang }}</td>
                                        <td style="text-align: center; vertical-align: middle">{{ $order->expense->name }}</td>
                                        <td style="text-align: center; vertical-align: middle">{{ $order->ghichu }}</td>

                                        <td style="text-align: center; vertical-align: middle;">
                                            @if (!empty($order->excel_file))
                                                <a href="{{ asset($order->excel_file) }}" download class="download-button" onclick="window.location.reload();">
                                                    <i class="ri ri-file-excel-2-line" style="color: green;"> Tải file</i>
                                                </a>
                                            @else
                                                Không có file
                                            @endif
                                        </td>
                                        <td style="text-align: center; vertical-align: middle; background-color: {{ $order->status == 0 ? 'yellow' : 'green' }};">
                                            {{ $order->status == 0 ? 'Chưa in' : 'Đã in' }}
                                        </td>
                                        <td style="text-align: center;vertical-align: middle;color: {{ $order->isEqual ? 'green' : 'red' }};">
                                            @if($order->isEqual)
                                                Hoàn thành
                                            @else
                                                Chưa hoàn thành
                                            @endif
                                        </td>
                                        <td style="text-align: center; vertical-align: middle;" class="no-modal-trigger">
                                            <button  class="btn btn-danger xoadonhang" title="Xóa đơn hàng"><i class="ri ri-delete-bin-5-line"></i></button>
                                            {{-- <button class="btn btn-secondary suaDonHang" data-order-id="{{$order->id}}" data-bs-target="#chinhSuaVatTuDonHang" data-bs-toggle="modal" title="Sửa đơn hàng"><i class="bi bi-pencil-square"></i></button> --}}
                                            <button class="btn btn-success checkThongTin" data-order-id="{{$order->id}}" data-bs-target="#danhMucVatTuChiTiet" data-bs-toggle="modal" title="Xem thông tin đơn hàng"><i class="ri-file-list-3-line"></i></button>
                                            {{-- <button class="btn btn-info historyOrder" data-order-id="{{$order->id}}" data-bs-target="#lichSuOrder" data-bs-toggle="modal" title="Lịch sử"><i class="bi bi-clock-history"></i></button> --}}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
              </div>
        </div>
    </div>
{{-- MODAL HIỂN THỊ DANH MỤC VẬT TƯ--}}
    <div class="modal fade" id="vautucuadanhmuc" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Danh mục vật tư</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h5 class="modal-title">Danh sách danh mục</h5>
                    <div class="table-responsive" style="max-height: 400px;">
                         <table class="table table-borderless table-bordered table-hover mt-2 danhmucvattulist" >
                            <thead>
                                <tr>
                                    <th style="text-align: center" scope="col">Stt</th>
                                    <th style="text-align: center" scope="col">Thương hiệu</th>
                                    <th style="text-align: center" scope="col">Dự án</th>
                                    <th style="text-align: center" scope="col">Tên danh mục vật tư</th>
                                    <th style="text-align: center" scope="col">Nhà cung cấp</th>
                                    <th style="text-align: center" scope="col">Mô tả</th>
                                    <th style="text-align: center" scope="col">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>

                    <hr> <!-- Đường kẻ ngang phân chia -->
                    <div id="chiTietVatTu" class="mt-1"> <!-- Khu vực để hiển thị thông tin chi tiết vật tư -->
                        <h5 class="modal-title titleDanhMuc">Vật tư danh mục:</h5>
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-borderless table-bordered mt-2 vattuchitietcuadanhmuc" >
                                <thead>
                                    <tr>
                                        <th style="text-align: center; vertical-align: middle;">STT</th>
                                        <th style="text-align: center; vertical-align: middle;">Tên vật tư</th>
                                        <th style="text-align: center; vertical-align: middle;">Mã số</th>
                                        <th style="text-align: center; vertical-align: middle;">Mã số mới</th>
                                        <th style="text-align: center; vertical-align: middle;">Đơn vị tính</th>
                                        <th style="text-align: center;">Số lượng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <td style="text-align: center" colspan="9" scope="col">Vui lòng chọn danh mục tạo đơn hàng</td>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary " id="creatDonHang" style="display: none"><i class="bi bi-cart-plus"></i> Tạo đơn hàng</button>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở lại</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- MODAL CHỈNH SỬA VẬT TƯ ĐƠN HÀNG--}}
    {{-- <div class="modal fade" id="chinhSuaVatTuDonHang" tabindex="-1" aria-labelledby="vatTuChiTietModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" >Danh sách vật tư</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 id="abcdxyz">Danh mục vật tư:</h6>
                    <table class="table table-borderless table-bordered mt-2 vatTuDonHangChinhSua">
                        <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">Chọn</th>
                                <th style="text-align: center; vertical-align: middle;">STT</th>
                                <th style="text-align: center; vertical-align: middle;">Tên vật tư</th>
                                <th style="text-align: center; vertical-align: middle;">Mã số</th>
                                <th style="text-align: center; vertical-align: middle;">Đơn vị tính</th>
                                <th style="text-align: center;">Số lượng</th>
                                <th style="text-align: center;">Số lượng đã chọn</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary editvattu">Chỉnh sửa</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div> --}}
{{-- CARD IN TRÌNH KÝ --}}
    <div id="printCard" class="card" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center; z-index: 1050; padding: 40px;">
        <div class="card-body" style="width: 400mm; height: 210mm; background-color: white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px; margin: auto; font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; overflow: auto;">
            <div class="table-responsive">
                <table class="table" style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; vertical-align: middle; width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-color: rgb(32, 30, 30); border-bottom: 3px solid rgb(32, 30, 30);">
                            <th style="text-align: left; vertical-align: middle; width: 20%; background-color: white !important; color: black !important">
                                <img src="{{ asset('assets/img/logo.png') }}" alt="logo" style="height: 25px;">
                            </th>
                            <th style="text-align: center; vertical-align: middle; font-size: 16px; background-color: white !important; color: black !important">

                            </th>
                            <!-- Cột mã biểu mẫu -->
                            <th style="text-align: center; vertical-align: middle; font-size: 14px; width: 20%; background-color: white !important; color: black !important">
                                QT.VPCL.TM 01-BM06
                            </th>
                        </tr>
                        <tr style="border-color: rgb(253, 253, 253);">
                            <!-- Cột logo -->
                            <th style="text-align: left; vertical-align: middle; width: 20%; background-color: white !important; color: black !important">

                            </th>
                            <!-- Cột thông tin công ty -->
                            <th style="text-align: center; vertical-align: middle; font-size: 16px; background-color: white !important; color: black !important">
                                <div id="companyName">...</div>
                                <div>KCN Cơ Khí Ô tô Chu Lai Trường Hải, Xã Tam Hiệp, Huyện Núi Thành, Tỉnh Quảng Nam, Việt Nam</div>
                                <div id="contactInfo">
                                    <span id="companyTel">Tel: ...</span> - <span id="companyFax">Fax:...</span>
                                </div>
                                <div id="companyTax">MST: ...</div>
                            </th>

                            <!-- Cột mã biểu mẫu -->
                            <th style="text-align: center; vertical-align: middle; font-size: 14px; width: 20%; background-color: white !important; color: black !important">

                            </th>
                        </tr>
                        <!-- Tiêu đề lớn ĐƠN ĐẶT HÀNG -->
                        <tr style="border-color: white;">
                            <th colspan="3" style="text-align: center; font-size: 20px; vertical-align: middle; background-color: white !important; color: black !important">
                                ĐƠN ĐẶT HÀNG
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; padding: 20px;">
                <div style="text-align: right;">
                    Số: (Sẽ được tạo ra khi hoàn thành phiếu)
                </div>
                <div style="text-align: left;" id="kinhGuiCard">
                    <strong style="text-decoration: underline; font-size: 14px">Kính gửi: ...............................</strong>
                </div>
                <div style="text-align: left;">
                    Địa chỉ: KCN TAM HIỆP - NÚI THÀNH - QUẢNG NAM
                </div>
                <div style="text-align: left;">
                    Đề nghị quý Công ty cung cấp số lượng hàng hóa theo bảng kê sau:
                </div>
            </div>
            <div class="table-responsive">
                <table class="table" id="tableInTrinhKy" style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; vertical-align: middle; width: 100%; border-collapse: collapse; border: 1px solid black;">
                    <thead>
                        <tr>
                            <th style="text-align: center; border: 1px solid black;">STT</th>
                            <th style="text-align: center; border: 1px solid black;">Mã vật tư</th>
                            <th style="text-align: center; border: 1px solid black;">Tên vật tư</th>
                            <th style="text-align: center; border: 1px solid black;">ĐVT</th>
                            <th style="text-align: center; border: 1px solid black;">SL</th>
                            <th style="text-align: center; border: 1px solid black;">Đơn giá (VNĐ)</th>
                            <th style="text-align: center; border: 1px solid black;">Thành tiền (VNĐ)</th>
                            <th style="text-align: center; border: 1px solid black;">Ngày yêu cầu hoàn thành</th>
                            <th style="text-align: center; border: 1px solid black;">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; padding: 20px;">
                <div style="text-align: left;">
                    <strong style="text-decoration: underline;">Yêu cầu:</strong>
                </div>
                <ul style="text-align: left; list-style-type: none; padding-left: 0;">
                    <li>- Báo giá: Không bao gồm thuế VAT.</li>
                    <li>- Nhận được Đơn hàng này đề nghị Bên bán xác nhận lại cho Bên mua.</li>
                    <li>- Giao hàng phải kèm theo đầy đủ chứng từ liên quan.</li>
                    <li>- Địa điểm giao hàng: kho Trung tâm R&D Ô tô.</li>
                </ul>
                <div style="text-align: left;">
                    Trân trọng!
                </div>
            </div>
            <div style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; padding: 20px;">
                <div id="ngayHoanThanhCard" style="text-align: right;">
                    Quảng Nam, ngày ... tháng ... năm ...
                </div>
                <div class="signatures" style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 40px;">
                    <div class="signature" style="text-align: center; flex: 1;">
                        <strong>Duyệt</strong>
                        <div class="signature-line" style="margin-top: 90px; "></div>
                        <div>Phan Quỳnh Trung</div>
                    </div>
                    <div class="signature" style="text-align: center; flex: 1;">
                        <strong>Kế toán</strong>
                        <div class="signature-line" style="margin-top: 90px; "></div>
                        <div>Nguyễn Văn Thứ</div>
                    </div>
                    <div class="signature" style="text-align: center; flex: 1;">
                        <strong>Kiểm tra</strong>
                        <div class="signature-line" style="margin-top: 90px; "></div>
                        <div>Phan Thanh Tài</div>
                    </div>
                    <div class="signature" style="text-align: center; flex: 1;">
                        <strong>Người lập</strong>
                        <div class="signature-line" style="margin-top: 90px; "></div>
                        <div>Nguyễn Thị Thu Yên</div>
                    </div>
                </div>
            </div>
            <div style="text-align: left; margin-top: 20px; font-size: 16px;">
                <strong>Xác nhận đặt đơn hàng</strong>
            </div>
            <div style="text-align: left; margin-top: 5px;">
                .................................................................................................................
            </div>
            <div style="text-align: left; margin-top: 5px;">
                ..................................................................................................................
            </div>
            <div style="text-align: left; margin-top: 5px;">
                ..................................................................................................................
            </div>
            <div style="text-align: left; margin-top: 5px;">
                ..................................................................................................................
            </div>
            <hr>
            <div style="text-align: center; margin-top: 20px;">
                <button type="button" class="btn btn-success" style="margin-right: 10px;" id="taoDonHangMoi"><i class="bi bi-check-circle"></i> Hoàn thành</button>
                <button type="button" class="btn btn-danger" style="margin-right: 10px;" id="huyDonHang"><i class="bi bi-x-circle"></i> Hủy bỏ</button>
            </div>
        </div>
    </div>
{{-- CARD ĐÃ KÝ --}}
    <div id="printCardDaKy" class="card" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center; z-index: 1050; padding: 10px;">
        <div class="card-body" style="max-width: 100%; height: auto; background-color: white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px; margin: auto; font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; overflow: auto;">
            <button type="button" class="close donglaimodal" aria-label="Close" style="position: absolute; top: 10px; right: 10px; width: 30px; height: 30px; background: #fff; border-radius: 15px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; cursor: pointer;" onclick="$('#printCardDaKy').hide();">
                <span style="color: #333;">&times;</span>
            </button>
            <div class="table-responsive">
                <div class="table" style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; vertical-align: middle; width: 100%; border-collapse: collapse;">
                    <div class="thead">
                        <div class="tr" style="display: flex; border-bottom: 3px solid rgb(32, 30, 30); flex-wrap: wrap;">
                            <div class="th" style="text-align: left; vertical-align: middle; width: 30%; background-color: white; color: black;">
                                <img src="{{ asset('assets/img/logo.png') }}" alt="logo" style="height: 25px;">
                            </div>
                            <div class="th" style="text-align: center; vertical-align: middle; flex: 1; font-size: 16px; background-color: white; color: black;">
                            </div>
                            <div class="th" style="text-align: center; vertical-align: middle; font-size: 12px; width: 20%; background-color: white; color: black;">
                                <strong>QT.VPCL.TM 01-BM06</strong>
                            </div>
                        </div>
                        <div style="display: flex; border-bottom: 1px solid rgb(253, 253, 253); flex-wrap: wrap;">
                            <div class="th" style="text-align: center; vertical-align: middle; flex: 1; font-size: 16px; background-color: white; color: black;">
                                <strong><div id="companyNameDaKy">...</div></strong>
                                <div><strong>KCN Cơ Khí Ô tô Chu Lai Trường Hải, Xã Tam Hiệp, Huyện Núi Thành, Tỉnh Quảng Nam, Việt Nam</strong></div>
                                <div id="contactInfoDaKy">
                                    <strong><span id="companyTelDaKy">Tel: ...</span></strong> - <strong><span id="companyFaxDaky">Fax:...</span></strong>
                                </div>
                                <strong><div id="companyTaxDaKy">MST: ...</div></strong>
                            </div>
                        </div>
                        <div class="tr" style="display: flex; border-bottom: 1px solid white; flex-wrap: wrap;">
                            <div class="th" style="text-align: center; flex: 1; font-size: 20px; vertical-align: middle; background-color: white; color: black;">
                                <strong style="margin-left: 150px">ĐƠN ĐẶT HÀNG</strong>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 5px; background-color: white; color: black;">
                                <div id="QRCode" style="margin-bottom: 5px;">
                                    <!-- QR Code content here -->
                                </div>
                                <div id="soDaKy" style="text-align: center;">
                                    Số: RD_010524_CSC_BUS
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; padding: 20px;">
                <div style="text-align: left;" id="kinhGuiCardDaKy">
                    <strong style="text-decoration: underline; font-size: 14px">Kính gửi: ...............................</strong>
                </div>
                <div style="text-align: left;">
                    Địa chỉ: KCN TAM HIỆP - NÚI THÀNH - QUẢNG NAM
                </div>
                <div style="text-align: left;">
                    Đề nghị quý Công ty cung cấp số lượng hàng hóa theo bảng kê sau:
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered" id="tableInTrinhKyDaKy" style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; vertical-align: middle; width: 100%; border-collapse: collapse; border: 1px solid black;">
                    <thead>
                        <tr>
                            <th style="width: 5%; text-align: center; border: 1px solid black;">STT</th>
                            <th style="width: 10%; text-align: center; border: 1px solid black;">Mã vật tư</th>
                            <th style="width: 20%; text-align: center; border: 1px solid black;">Tên vật tư</th>
                            <th style="width: 10%; text-align: center; border: 1px solid black;">ĐVT</th>
                            <th style="width: 5%; text-align: center; border: 1px solid black;">SL</th>
                            <th style="width: 15%; text-align: center; border: 1px solid black;">Đơn giá (VNĐ)</th>
                            <th style="width: 15%; text-align: center; border: 1px solid black;">Thành tiền (VNĐ)</th>
                            <th style="width: 10%; text-align: center; border: 1px solid black;">Ngày yêu cầu hoàn thành</th>
                            <th style="width: 10%; text-align: center; border: 1px solid black;">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; padding: 20px;">
                <div style="text-align: left;">
                    <strong style="text-decoration: underline;">Yêu cầu:</strong>
                </div>
                <ul style="text-align: left; list-style-type: none; padding-left: 0;">
                    <li>- Báo giá: Không bao gồm thuế VAT.</li>
                    <li>- Nhận được Đơn hàng này đề nghị Bên bán xác nhận lại cho Bên mua.</li>
                    <li>- Giao hàng phải kèm theo đầy đủ chứng từ liên quan.</li>
                    <li>- Địa điểm giao hàng: kho Trung tâm R&D Ô tô.</li>
                </ul>
                <div style="text-align: left;">
                    Trân trọng!
                </div>
            </div>
            <div style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif; padding: 20px;">
                <div id="ngayHoanThanhDaKy" style="text-align: right;">
                    Quảng Nam, ngày ... tháng ... năm ...
                </div>
                <div class="signatures" style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; margin-top: 40px;">
                    <div class="signature" style="text-align: center; flex: 1;">
                        <strong>Duyệt</strong>
                        <div class="signature-line" style="margin-top: 90px;"></div>
                        <div>Phan Quỳnh Trung</div>
                    </div>
                    <div class="signature" style="text-align: center; flex: 1;">
                        <strong>Kế toán</strong>
                        <div class="signature-line" style="margin-top: 90px;"></div>
                        <div>Nguyễn Văn Thứ</div>
                    </div>
                    <div class="signature" style="text-align: center; flex: 1;">
                        <strong>Kiểm tra</strong>
                        <div class="signature-line" style="margin-top: 90px;"></div>
                        <div>Phan Thanh Tài</div>
                    </div>
                    <div class="signature" style="text-align: center; flex: 1;">
                        <strong>Người lập</strong>
                        <div class="signature-line" style="margin-top: 90px;"></div>
                        <div>Nguyễn Thị Thu Yên</div>
                    </div>
                </div>
            </div>
            <div style="text-align: left; margin-top: 20px; font-size: 16px;">
                <strong>Xác nhận đặt đơn hàng</strong>
            </div>
            <div style="text-align: left; margin-top: 5px;">
                .................................................................................................................
            </div>
            <div style="text-align: left; margin-top: 5px;">
                ..................................................................................................................
            </div>
            <hr class="koin">
            <div style="text-align: center; margin-top: 20px;">
                <button type="button" class="btn btn-success" style="margin-right: 10px;" id="inTrinhKy">
                    <i class="bi bi-printer"></i> In Trình Ký
                </button>
            </div>
        </div>
    </div>
{{-- MODAL DANH MỤC VẬT TƯ CỦA ĐƠN HÀNG--}}
    <div class="modal fade" id="danhMucVatTuChiTiet" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vật tư chi tiết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="location.reload();"></button>
                </div>
                <div class="modal-body">
                    <div class="filter-box mt-3">
                        <div class="row">
                            <div class="col-sm-2" style="width: 10%">
                                <select class="tenvattuchitiet" aria-label="Default select example" >
                                    <option selected="">Tên vật tư</option>
                                </select>
                            </div>
                            <div class="col-sm-2" style="width: 10%">
                                <select class="masochitiet" aria-label="Default select example" >
                                    <option selected="">Mã số</option>
                                </select>
                            </div>
                            <div class="col-sm-1" style="width: 10%">
                                <select class="form-select donvitinhchitiet" aria-label="Default select example" >
                                    <option selected="">Chọn đơn vị tính</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" id="searchVatTuChiTiet" class="btn btn-primary" style="margin-left: -5px">Tìm kiếm</button>
                            </div>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center;">
                        <h6 class="modal-title" id="orderTitle">Đơn hàng:</h6>
                        <span style="margin-left: 8px;">|</span>
                        <h6 style="margin-left: 8px;" class="modal-title" id="tongsovattu">Tổng vật tư:</h6>
                        <span style="margin-left: 8px;">|</span>
                        <h6 style="margin-left: 8px;" class="modal-title" id="tongdanhan">Tổng đã nhận:</h6>
                        <span style="margin-left: 8px;">|</span>
                        <h6 style="margin-left: 8px;" class="modal-title" id="tongchuanhan">Tổng chưa nhận:</h6>
                        <span style="margin-left: 8px;">|</span>
                        <h6 style="margin-left: 8px;" class="modal-title" id="tongdaxuat">Tổng đã xuất:</h6>
                    </div>
                    <div class="table-responsive" style="max-height: 650px;">
                        <table class="table table-bordered table-hover danhmucvattuchitiet">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">STT</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Tên vật tư</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Mã số</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Mã số mới</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Đơn vị tính</th>
                                    <th colspan="5" style="text-align: center;">Tình trạng</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">ghi chú</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center; vertical-align: middle;">Tổng</th>
                                    <th style="text-align: center; vertical-align: middle;">Đã nhận</th>
                                    <th style="text-align: center; vertical-align: middle;">Lưu kho</th>
                                    <th style="text-align: center; vertical-align: middle;">Chưa nhận</th>
                                    <th style="text-align: center; vertical-align: middle;">Đã xuất</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="location.reload();">Trở lại</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- LỊCH SỬ ĐƠN HÀNG--}}
    <div class="modal fade" id="lichSuOrder" style="background-color: #000000bb" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lịch sử giao dịch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive" style="max-height: 450px;">
                        <table class="table table-bordered lichSuOrder">
                            <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">STT</th>
                                <th style="text-align: center; vertical-align: middle;">Nội dung thay đổi</th>
                            </tr>
                            </thead>
                            <tbody>
                                <!-- Nội dung lịch sử sẽ được tải động vào đây -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- MODAL VẬT TƯ HẾT HÀNG --}}
    <div class="modal fade" id="outOfStockModal" tabindex="-1" aria-labelledby="outOfStockModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="outOfStockModalLabel">Danh sách vật tư hết hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive" style="max-height: 450px;">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; vertical-align: middle;">
                                            <input class="form-check-input" id="selectAllCheckBox" type="checkbox">
                                        </th>
                                        <th style="text-align: center; vertical-align: middle;">STT</th>
                                        <th style="text-align: center; vertical-align: middle;">Tên vật tư</th>
                                        <th style="text-align: center; vertical-align: middle;">Mã số</th>
                                        <th style="text-align: center; vertical-align: middle;">Mã số mới</th>
                                        <th style="text-align: center; vertical-align: middle;">Đơn vị tính</th>
                                        <th style="text-align: center;">Số lượng</th>
                                        <th style="text-align: center;">Danh mục vật tư</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Thêm các hàng dữ liệu tại đây -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

</section>
@endsection
@section('script')
    <script src="{{asset('assets/js/select2.min.js')}}"></script>
    {{-- HIỂN THỊ MODAL CHỨA DANH MỤC VẬT TƯ CHƯA TẠO ĐƠN HÀNG --}}
        <script>
            $(document).ready(function() {
                // Bắt sự kiện click vào nút "Danh mục đang chờ"
                $('.danhmucdangcho').on('click', function() {
                    $('#vautucuadanhmuc').modal('show'); // Hiển thị modal danh sách danh mục

                    // Gửi yêu cầu AJAX để lấy danh sách danh mục chưa có đơn hàng
                    $.ajax({
                        type: 'POST',
                        url: '{{ route("layDanhMucChuaCoDonHang") }}',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            $('.danhmucvattulist tbody').empty();

                            if (data.length > 0) {
                                data.forEach(function(catalog, index) {
                                    var newRow = `<tr data-kinhgui="${catalog.provider_info.describe}" data-catalog-id="${catalog.id}" data-project-name="${catalog.project.name}" data-catalog-name="${catalog.name}">
                                        <td style="text-align: center">${index + 1}</td>
                                        <td style="text-align: center">${catalog.project.segment.brand.name}</td>
                                        <td style="text-align: center">${catalog.project.name}</td>
                                        <td style="text-align: center">${catalog.name}</td>
                                        <td style="text-align: center">${catalog.nhacungcap}</td>
                                        <td style="text-align: center">${catalog.description || 'Không có mô tả'}</td>
                                        <td style="text-align: center">
                                            <button type="button" data-nhacungcap="${catalog.nhacungcap}" class="btn btn-secondary tao-don-hang" title="Thông tin danh mục"><i class="bi bi-cart4"></i></button>
                                        </td>
                                    </tr>`;
                                    $('.danhmucvattulist tbody').append(newRow);
                                });
                            } else {
                                $('.danhmucvattulist tbody').append('<tr><td colspan="7" style="text-align: center">Không có danh mục nào.</td></tr>');
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Có lỗi xảy ra khi tải dữ liệu: ' + error);
                        }
                    });
                });

                // Bắt sự kiện click vào nút "tao-don-hang"
                $(document).on('click', '.tao-don-hang', function() {
                    var catalogId = $(this).closest('tr').data('catalog-id'); // Lấy ID của danh mục
                    var catalogName = $(this).closest('tr').data('catalog-name');
                    var projectName = $(this).closest('tr').data('project-name');
                    var kinhgui = $(this).closest('tr').data('kinhgui');
                    var nhacungcap = $(this).data('nhacungcap');
                    $('#chiTietVatTu .titleDanhMuc').text('Vật tư danh mục: ' + catalogName); // Cập nhật tiêu đề phần chi tiết
                    $('#timKiemVatTuChiTietOfDm').attr('data-catalog-id', catalogId);
                    $('#creatDonHang').attr('data-projectName', projectName);
                    $('#creatDonHang').attr('data-kinhgui', kinhgui);
                    $('#creatDonHang').attr('data-nhacungcap', nhacungcap);

                    // Gửi yêu cầu AJAX để lấy thông tin chi tiết vật tư của danh mục được chọn
                    $.ajax({
                        type: 'POST',
                        url: '{{ route("duLieuVatTuCuaDanhMuc") }}',
                        data: {
                            id: catalogId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            $('.vattuchitietcuadanhmuc tbody').empty();
                            if (data.supplies.length > 0) {
                                data.supplies.forEach(function(supply, index) {
                                    var noteIcon = supply.note ? `<span class="bi bi-info-circle-fill text-info" style="cursor:pointer;" data-bs-toggle="popover" title="Nguyên nhân" data-bs-content="${supply.note}"></span>` : '';
                                    var disabledClass = supply.remaining_quantity === 0 ? 'disabled-row' : '';
                                    var supplyRow = `<tr class="${disabledClass}" data-id="${supply.id}"
                                            data-tenvattu="${supply.tenvattu}"
                                            data-maso="${supply.maso}"
                                            data-donvitinh="${supply.donvitinh}"
                                            data-soluong="${supply.remaining_quantity}"
                                            data-dongia="${supply.dongia || ''}"
                                            data-thanhtien="${supply.thanhtien || ''}"
                                            data-ngayyeucau="${supply.ngayyeucau || ''}"
                                            data-ghichu="${supply.ghichu || ''}">
                                        <td style="text-align: center">${index + 1}</td>
                                        <td style="text-align: center">${supply.tenvattu} ${noteIcon}</td>
                                        <td style="text-align: center">${supply.maso}</td>
                                         <td style="text-align: center">${supply.maso_new ?? ""}</td>
                                        <td style="text-align: center">${supply.donvitinh}</td>
                                        <td style="text-align: center">${supply.remaining_quantity}</td>
                                    </tr>`;
                                    $('.vattuchitietcuadanhmuc tbody').append(supplyRow);
                                });
                                $('[data-bs-toggle="popover"]').popover();
                            } else {
                                $('.vattuchitietcuadanhmuc tbody').append('<tr><td colspan="8" style="text-align: center">Không có vật tư nào.</td></tr>');
                            }
                            // Hiển thị nút tạo đơn hàng sau khi tải dữ liệu thành công
                            $('#creatDonHang').show();
                        },
                        error: function() {
                            alert("Có lỗi xảy ra, không thể tải dữ liệu vật tư!");
                        }
                    });
                });
            });
        </script>


    {{-- HIỂN THỊ BIỂU MẤU SAU KHI CHỌN CHECKBOX --}}
        <script>
            $(document).ready(function() {
                // Khởi tạo và thiết lập select dropdown
                initializeCompanySelect();

                // Thiết lập sự kiện cho select
                setupSelectOnChange();

                // Thiết lập ngày hoàn thành
                setupDateInput();

                // Xử lý sự kiện khi tạo đơn hàng
                handleCreateOrder();

                // Xử lý sự kiện khi lưu phiếu đơn hàng
                handleSaveOrder();
            });

            var chiPhi = @json($chiPhi);

            function initializeCompanySelect() {
                var selectHTML = '<select id="companySelect" class="form-control">';
                // Thêm tùy chọn mặc định mới
                selectHTML += `<option value="" selected>Chọn nhà máy chi phí</option>`;
                chiPhi.forEach(function(expense) {
                    selectHTML += `<option value="${expense.id}"
                                    data-telephone="${expense.telephone ? expense.telephone : '...'}"
                                    data-fax="${expense.fax ? expense.fax : '...'}"
                                    data-tax-number="${expense.tax_number ? expense.tax_number : '...'}">
                                    ${expense.description}
                                    </option>`;
                });
                selectHTML += '</select>';
                $('#companyName').html(selectHTML);
            }

            function setupSelectOnChange() {
                $('#companySelect').change(function() {
                    var selected = $(this).find('option:selected');
                    $('#companyTel').text('Tel: ' + selected.data('telephone'));
                    $('#companyFax').text('Fax: ' + selected.data('fax'));
                    $('#companyTax').text('MST: ' + selected.data('tax-number'));
                }).trigger('change');
            }

            function setupDateInput() {
                var dateInputHTML = `<label for="ngayHoanThanh">Quảng Nam, ngày:</label>
                                    <input type="date" id="ngayHoanThanh" name="ngayHoanThanh" style="border: 1px solid #ccc; padding: 5px;">`;
                $('#ngayHoanThanhCard').html(dateInputHTML);
            }

            function handleCreateOrder() {
                $('#creatDonHang').click(function() {
                    var projectName = $(this).data('projectname'); // Lấy giá trị data-projectname trực tiếp từ nút
                    var kinhGui = $(this).data('kinhgui');
                    var nhacungcap = $(this).data('nhacungcap');
                    var selectedSupplies = $('.vattuchitietcuadanhmuc tbody tr').map(function() {
                        var row = $(this);
                        return {
                            id: row.data('id'),
                            tenvattu: row.find('td').eq(1).text(),
                            maso: row.find('td').eq(2).text(),
                            donvitinh: row.find('td').eq(4).text(),
                            soluong: row.find('td').eq(5).text(),

                        };
                    }).get();

                    // Kiểm tra điều kiện dấu "X" trong cột 6 hoặc số lượng row > 10
                    var hasMarkX = selectedSupplies.some(supply => supply.mark === "X");
                    var isMoreThanTen = selectedSupplies.length > 10;
                        updateOrderTable(selectedSupplies, projectName, kinhGui, nhacungcap, hasMarkX, isMoreThanTen);
                });
            }

            function updateOrderTable(supplies, projectName, kinhGui, nhacungcap, hasMarkX, isMoreThanTen) {
                var tbody = $('#tableInTrinhKy tbody');
                tbody.empty();  // Làm sạch bảng hiện tại

                if (hasMarkX || isMoreThanTen) {
                    // Tạo một hàng cho upload file và bao gồm cả ngày yêu cầu hoàn thành và dự án
                    var hiddenInputs = supplies.map(supply => `<input type="hidden" name="supplyIds[]" value="${supply.id}">`).join('');
                    var uploadRow = `<tr data-nhacungcap="${nhacungcap}" style="border: 1px solid black;">
                                        <td colspan="2" style="text-align: center; border: 1px solid black;">
                                            <input type="file" class="form-control fileDonHang" />
                                            ${hiddenInputs}
                                        </td>
                                        <!-- 5 cột tiếp theo, mỗi cột chứa một input để nhập dữ liệu -->
                                        <td style="text-align: center; border: 1px solid black;"><input type="text" id="noidungInput" class="form-control" placeholder="Tên list danh mục vật tư" /></td>
                                        <td style="text-align: center; border: 1px solid black;">List</td>
                                        <td style="text-align: center; border: 1px solid black;">1</td>
                                        <td style="text-align: center; border: 1px solid black;"></td>
                                        <td style="text-align: center; border: 1px solid black;"></td>
                                        <td style="text-align: center; border: 1px solid black;"><input type="date" id="ngayHoanThanhInput" class="form-control" /></td>
                                        <td style="text-align: center; border: 1px solid black;">Dự án: ${projectName}</td>
                                    </tr>`;
                    tbody.append(uploadRow);
                } else {
                    // Tạo hàng như bình thường
                    supplies.forEach(function(supply, index) {
                        var newRow = createSupplyRow(supply, index, supplies.length, projectName, nhacungcap);
                        tbody.append(newRow);
                    });
                }

                $("#kinhGuiCard").html(`<strong style="text-decoration: underline; font-size: 20px">Kính gửi: ${kinhGui}</strong>`);
                $('#printCard').css('display', 'flex');
                $('#vautucuadanhmuc').modal('hide');
            }

            function createSupplyRow(supply, index, totalSupplies, projectName, nhacungcap) {
                return `<tr data-supply-id="${supply.id}" data-nhacungcap="${nhacungcap}">
                    <td style="text-align: center; border: 1px solid black;">${index + 1}</td>
                    <td style="text-align: center; border: 1px solid black;">${supply.maso}</td>
                    <td style="text-align: center; border: 1px solid black;">${supply.tenvattu}</td>
                    <td style="text-align: center; border: 1px solid black;">${supply.donvitinh}</td>
                    <td style="text-align: center; border: 1px solid black;">${supply.soluong}</td>
                    <td style="text-align: center; border: 1px solid black;">
                        <input type="number" name="quantity_${supply.id}" class="form-control" tabindex="1" />
                    </td>
                    <td style="text-align: center; border: 1px solid black;">
                        <input type="number" name="price_${supply.id}" class="form-control" tabindex="2" />
                    </td>
                    ${index === 0 ? `<td rowspan="${totalSupplies}" style="text-align: center; border: 1px solid black;">
                        <input type="date" id="ngayHoanThanhInput" class="form-control" name="ngayHoanThanh" />
                    </td>` : ''}
                    ${index === 0 ? `<td rowspan="${totalSupplies}" style="text-align: center; border: 1px solid black;">Dự án:${projectName}</td>` : ''}
                </tr>`;
            }
        </script>

    {{-- TẠO ĐƠN HÀNG --}}
        <script>
            $(document).ready(function() {
                $('#taoDonHangMoi').on('click', function() {
                    var allDatesFilled = true;
                    var fileInput = $('#tableInTrinhKy input[type="file"]');
                    var file = fileInput.val();

                    // Kiểm tra xem input file có tồn tại và có đang bị trống không
                    if (fileInput.length > 0 && !file) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'File không được để trống!',
                            text: 'Bạn phải chọn một file để tiếp tục.',
                            confirmButtonText: 'Đóng'
                        });
                        return; // Dừng hàm nếu không có file được chọn
                    }

                    var ngayHoanThanh = $('#ngayHoanThanh').val();
                    if (!ngayHoanThanh) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Thiếu thông tin!',
                            text: 'Bạn cần điền ngày tạo đơn hàng.',
                            confirmButtonText: 'Đóng'
                        });
                        allDatesFilled = false;
                        return;
                    }

                    var companyId = $('#companySelect').val();
                    if (!companyId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Thiếu thông tin công ty!',
                            text: 'Vui lòng chọn một công ty từ danh sách thả xuống.',
                            confirmButtonText: 'Đóng'
                        });
                        return;
                    }

                    $('#tableInTrinhKy tbody tr').each(function() {
                        $(this).find('td').each(function() {
                            var input = $(this).find('input[type="date"]');
                            if (input.length > 0 && !input.val()) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Thiếu thông tin!',
                                    text: 'Bạn cần điền ngày hoàn thành.',
                                    confirmButtonText: 'Đóng'
                                });
                                allDatesFilled = false;
                                return false;
                            }
                        });
                        if (!allDatesFilled) {
                            return false;
                        }
                    });

                    if (!allDatesFilled) return;

                    // Chuyển đổi các giá trị input và select thành text
                    $('#tableInTrinhKy tbody tr').each(function() {
                        $(this).find('td').each(function() {
                            var input = $(this).find('input');
                            if (input.length > 0 && input.attr('type') !== 'file') { // Bỏ qua nếu là input file
                                if (input.val() === "") {
                                    $(this).text(""); // Set the text to empty if input is empty
                                } else {
                                    var value = input.val();
                                    var formattedValue = (input.attr('type') === 'number') ? parseInt(value).toLocaleString('en-US') : value;
                                    $(this).text(formattedValue);
                                    if (input.attr('id') === 'noidung') {
                                        var targetColumnIndex = 8; // Giả sử cột Ghi chú là cột muốn hiển thị dữ liệu từ input noidung
                                        var row = $(this).parent();
                                        row.find('td').eq(targetColumnIndex).text(formattedValue);
                                    }
                                }
                            }
                        });
                    });

                    // Đoạn mã cập nhật thông tin công ty
                    var companyId = $('#companySelect option:selected').val();
                    var companyName = $('#companySelect option:selected').text();

                    $('#companyName').html(companyName + `<input type="hidden" id="hiddenCompanyId" value="${companyId}">`);
                    $('#companyTel').text('Tel: ' + $('#companyTel').text().replace('Tel: ', ''));
                    $('#companyFax').text('Fax: ' + $('#companyFax').text().replace('Fax: ', ''));
                    $('#companyTax').text('MST: ' + $('#companyTax').text().replace('MST: ', ''));

                    var date = new Date(ngayHoanThanh);
                    var formattedDate = `Quảng Nam, ngày ${date.getDate()} tháng ${date.getMonth() + 1} năm ${date.getFullYear()}`;
                    $('#ngayHoanThanhCard').html(formattedDate + `<input type="hidden" id="hiddenNgayHoanThanh" value="${ngayHoanThanh}">`);

                    var hasSpecialRow = $('#tableInTrinhKy tbody tr').length === 1 && $('#tableInTrinhKy tbody tr input[type="file"]').length === 1;
                    var formData = new FormData();
                    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                    formData.append('companyId', $('#hiddenCompanyId').val());
                    formData.append('nhacungcap', $('#tableInTrinhKy tbody tr:first').data('nhacungcap'));
                    formData.append('ngayYeuCau', $('#hiddenNgayHoanThanh').val());
                    formData.append('ngayHoanThanh', $('#tableInTrinhKy tbody tr:first').find('td').eq(hasSpecialRow ? 6 : 7).text());
                    formData.append('ghiChu', $('#tableInTrinhKy tbody tr:first').find('td').eq(hasSpecialRow ? 7 : 8).text());

                    if (hasSpecialRow) {
                        formData.append('noidung', $('#tableInTrinhKy tbody tr:first').find('td').eq(1).text());
                        $('input[name="supplyIds[]"]').each(function() {
                            formData.append('supplyIds[]', $(this).val());
                        });

                        var fileInput = $('#tableInTrinhKy input[type="file"]')[0];
                        if (fileInput.files.length > 0) {
                            formData.append('file', fileInput.files[0]);
                        } else {
                            alert('Please select a file to upload.');
                            return;
                        }
                    } else {
                        $('#tableInTrinhKy tbody tr').each(function() {
                            var supplyId = $(this).data('supply-id');
                            formData.append('supplyIds[]', supplyId);
                            var soluong = $(this).find('td').eq(4).text();
                            formData.append('soluong[]', soluong);

                            var quantity = $(this).find('td').eq(5).text();
                            var price = $(this).find('td').eq(6).text();
                            formData.append('quantities[]', quantity);
                            formData.append('prices[]', price);
                        });
                    }

                    $.ajax({
                        url: '{{ route("taoDonHangMoi") }}',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành Công',
                                text: 'Đơn hàng đã được tạo thành công.',
                                confirmButtonText: 'OK',
                                timer: 2000, // Đặt thời gian tự động đóng thông báo là 2000 milliseconds (2 giây)
                                timerProgressBar: true, // Hiển thị một thanh tiến trình cho thời gian đếm ngược
                                willClose: () => {
                                    window.location.reload(); // Tự động tải lại trang khi thông báo đóng
                                }
                            });
                        },
                        error: function(error) {
                            console.error('Error sending data:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Không thể tạo đơn hàng. Vui lòng thử lại.',
                                confirmButtonText: 'Đóng'
                            });
                        }
                    });
                });
            });
        </script>


    {{-- XÓA ĐƠN HÀNG --}}
        <script>
            $(document).ready(function() {
                $('.xoadonhang').on('click', function() {
                    var orderId = $(this).closest('tr').data('order-id'); // Lấy ID của đơn hàng từ attribute data-order-id của dòng hiện tại

                    Swal.fire({
                        title: 'Bạn có chắc chắn?',
                        text: "Bạn muốn hủy đơn hàng này không?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Có, hủy nó!',
                        cancelButtonText: 'Không, giữ lại!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                type: 'POST',
                                url: '{{ route("deleteDonHang") }}',
                                data: {
                                    orderId: orderId,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Đã Xóa!',
                                        'Đơn hàng đã được hủy thành công.',
                                        'success'
                                    );
                                    // Tải lại trang hoặc xóa dòng từ DOM, tùy vào cách bạn muốn cập nhật UI
                                    location.reload(); // Hoặc $(this).closest('tr').remove();
                                },
                                error: function() {
                                    Swal.fire(
                                        'Lỗi!',
                                        'Không thể xóa đơn hàng. Vui lòng thử lại.',
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });
            });
        </script>
    {{-- HIỂN THỊ ĐƠN HÀNG ĐÃ TẠO --}}
        <script>
            $(document).ready(function() {
                $('.vattuchitietdonhang tbody tr').click(function(event) {
                    if ($(event.target).closest('.no-modal-trigger').length) {
                        // Nếu có, không làm gì cả để ngăn chặn hiển thị modal
                        return;
                    }
                    var orderId = $(this).data('order-id');
                    $.ajax({
                        url: "{{ route('duLieuDaKy') }}",
                        type: 'POST',
                        data: {
                            id: orderId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            console.log(data);
                            $('#QRCode').html(data.qrCode);
                            $('#inTrinhKy').attr('data-status', data.order.status);
                            $('#inTrinhKy').attr('data-id', data.order.id);
                            var ngayTaoPhieu = new Date(data.order.ngaytaophieu);
                            var formattedDate = `Quảng Nam, ngày ${ngayTaoPhieu.getDate()} tháng ${ngayTaoPhieu.getMonth() + 1} năm ${ngayTaoPhieu.getFullYear()}`;
                            $('#ngayHoanThanhDaKy').html(formattedDate);
                            $('#companyNameDaKy').text(data.expense.description);
                            $('#companyTelDaKy').text('Tel: ' + data.expense.telephone);
                            $('#companyFaxDaKy').text('Fax: ' + data.expense.fax);
                            $('#companyTaxDaKy').text('MST: ' + data.expense.tax_number);
                            $('#soDaKy').text('Số: ' + data.order.sodonhang);
                            $('#kinhGuiCardDaKy').html(`<strong style="text-decoration: underline; font-size: 16px">Kính gửi: ${data.providerInfo.describe}</strong>`);
                            $('#tableInTrinhKyDaKy tbody').empty()
                            if (data.order.excel_file) {
                                let downloadLink = `<a href="/path/to/files/${data.order.excel_file}" download="${data.order.excel_file.split('/').pop()}" class="btn btn-primary">Download File</a>`;
                                let newRow = `
                                    <tr>
                                        <td style="text-align: center;">1</td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;">${data.order.noidung}</td>
                                        <td style="text-align: center;">List</td>
                                        <td style="text-align: center;">1</td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center;"></td>
                                        <td style="text-align: center; border: 1px solid black;">${new Date(data.order.ngayhoanthanh).toLocaleDateString('vi-VN')}</td>
                                        <td style="text-align: center; border: 1px solid black;">Dự án: ${data.order.ghichu}</td>
                                    </tr>`;
                                $('#tableInTrinhKyDaKy tbody').append(newRow); // Thêm hàng mới vào bảng
                            }else {
                                data.supplies.forEach(function(supply, index) {
                                    let formattedDonGia = supply.don_gia ? parseInt(supply.don_gia).toLocaleString('vi-VN') : '';
                                    let formattedThanhTien = supply.thanh_tien ? parseInt(supply.thanh_tien).toLocaleString('vi-VN') : '';
                                    let ngayHoanThanh = new Date(data.order.ngayhoanthanh);
                                    let day = ngayHoanThanh.getDate().toString().padStart(2, '0');
                                    let month = (ngayHoanThanh.getMonth() + 1).toString().padStart(2, '0'); // Tháng trong JavaScript bắt đầu từ 0
                                    let year = ngayHoanThanh.getFullYear();
                                    let formattedNgayHoanThanh = `${day}/${month}/${year}`;


                                    let supplyRow = `
                                        <tr>
                                            <td style="text-align: center;">${index + 1}</td>
                                            <td style="text-align: center;">${supply.maso}</td>
                                            <td style="text-align: center;">${supply.tenvattu}</td>
                                            <td style="text-align: center;">${supply.donvitinh}</td>
                                            <td style="text-align: center;">${supply.soluong}</td>
                                            <td style="text-align: center;">${formattedDonGia}</td>
                                            <td style="text-align: center;">${formattedThanhTien}</td>`;
                                    if (index === 0) {
                                        supplyRow += `
                                            <td rowspan="${data.supplies.length}" style="text-align: center; border: 1px solid black;">
                                                ${formattedNgayHoanThanh}
                                            </td>
                                            <td rowspan="${data.supplies.length}" style="text-align: center; border: 1px solid black;">
                                                Dự án: ${data.order.ghichu}
                                            </td>`;
                                    }
                                    supplyRow += `</tr>`;
                                    $('#tableInTrinhKyDaKy tbody').append(supplyRow);
                                });
                            }
                            $('#printCardDaKy').show(); // Hiển thị modal
                        },
                        error: function(error) {
                            console.error('Error loading data:', error);
                            alert('Không thể tải dữ liệu, vui lòng thử lại.');
                        }
                    });
                });

                // Thêm sự kiện cho nút đóng hoặc hủy modal nếu có
                $('#huyDonHang').on('click', function() {
                    $('#printCardDaKy').hide(); // Ẩn modal
                });

                // Bất kỳ xử lý nút khác nếu cần
                $('#luuPhieuDonHang').on('click', function() {
                    // Xử lý lưu thông tin ở đây
                    console.log('Thông tin đơn hàng được lưu.');
                });

                // Khi người dùng muốn hoàn thành và đóng modal
                $('#taoDonHangMoi').on('click', function() {
                    // Có thể thực hiện lưu dữ liệu hoặc xác nhận trước khi đóng
                    console.log('Đơn hàng hoàn thành.');
                    $('#printCardDaKy').hide(); // Đóng modal
                });
            });
        </script>
    {{-- SỬA ĐƠN HÀNG --}}
        <script>
            $(document).ready(function() {
                var initialSupplies = []; // Mảng lưu trạng thái ban đầu của các vật tư
                var initialSuppliesDetails = {}; // Đối tượng lưu thông tin chi tiết của các vật tư ban đầu

                // SỬA ĐƠN HÀNG
                $('.suaDonHang').on('click', function() {
                    var orderId = $(this).data('order-id'); // Lấy ID của đơn hàng từ data attribute
                    $('.editvattu').data('order-id', orderId);
                    $.ajax({
                        url: '{{ route("suaDonHang") }}',
                        type: 'POST',
                        data: { order_id: orderId },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            console.log(response);
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            var tbody = $('#chinhSuaVatTuDonHang .vatTuDonHangChinhSua tbody');
                            tbody.empty(); // Xóa các hàng hiện tại trong bảng
                            initialSupplies = []; // Xóa trạng thái ban đầu cũ
                            initialSuppliesDetails = {}; // Xóa thông tin chi tiết ban đầu cũ

                            $.each(response.supplies, function(index, supplyInfo) {
                                var supply = supplyInfo.supply;
                                var checked = supplyInfo.selected_in_current_order ? 'checked' : '';
                                var disabled = supplyInfo.fully_selected_in_other_orders ? 'disabled' : '';
                                var trStyle = supplyInfo.fully_selected_in_other_orders ? 'style="opacity: 0.5;"' : '';
                                var quantityInputStyle = supplyInfo.selected_in_current_order ? '' : 'style="display:none;"';
                                var remainingQuantity = supplyInfo.remaining_quantity;

                                // Lưu trạng thái ban đầu và thông tin chi tiết
                                if (supplyInfo.selected_in_current_order) {
                                    initialSupplies.push(supply.id);
                                    initialSuppliesDetails[supply.id] = {
                                        tenvattu: supply.tenvattu,
                                        maso: supply.maso,
                                        selected_quantity: supplyInfo.selected_quantity_in_current_order,
                                        initial_remaining_quantity: remainingQuantity + supplyInfo.selected_quantity_in_current_order // lưu lại số lượng ban đầu
                                    };
                                }

                                // Xử lý hiển thị cột số lượng và checkbox
                                var checkboxColumn = `
                                    <td style="text-align: center; vertical-align: middle;">
                                        <input class="form-check-input supply-checkbox" type="checkbox" ${checked} ${disabled}>
                                    </td>`;

                                var quantityColumn = supplyInfo.selected_in_current_order
                                    ? `<td style="text-align: center; vertical-align: middle;">
                                        <input type="number" class="form-control supply-quantity" value="${supplyInfo.selected_quantity_in_current_order}" min="0" max="${remainingQuantity + supplyInfo.selected_quantity_in_current_order}">
                                    </td>`
                                    : `<td style="text-align: center; vertical-align: middle;">
                                        ${remainingQuantity > 0 ? `<input type="number" class="form-control supply-quantity" value="0" min="0" max="${remainingQuantity}" disabled>` : '0'}
                                    </td>`;

                                // Nếu vật tư đã được chọn hoàn toàn trong các đơn hàng khác, để cột số lượng rỗng
                                if (supplyInfo.fully_selected_in_other_orders) {
                                    quantityColumn = `<td style="text-align: center; vertical-align: middle;"></td>`;
                                }

                                tbody.append(`
                                    <tr data-supply-id="${supply.id}" ${trStyle}>
                                        ${checkboxColumn}
                                        <td style="text-align: center; vertical-align: middle;">${index + 1}</td>
                                        <td style="text-align: center; vertical-align: middle;">${supply.tenvattu}</td>
                                        <td style="text-align: center; vertical-align: middle;">${supply.maso}</td>
                                        <td style="text-align: center; vertical-align: middle;">${supply.donvitinh}</td>
                                        <td class="remaining-quantity" style="text-align: center; vertical-align: middle;">${remainingQuantity}</td>
                                        ${quantityColumn}
                                    </tr>
                                `);
                            });
                            $('#abcdxyz').text('Danh sách vật tư: ' + response.catalogName); // Cập nhật tên danh mục
                            $('#chinhSuaVatTuDonHang').modal('show'); // Hiển thị modal
                        },
                        error: function(xhr) {
                            alert('Error: ' + xhr.responseJSON.error);
                        }
                    });
                });

                // Hiển thị/ẩn input số lượng khi checkbox thay đổi trạng thái
                $(document).on('change', '.supply-checkbox', function() {
                    var checkbox = $(this);
                    var quantityInput = checkbox.closest('tr').find('.supply-quantity');
                    var supplyId = checkbox.closest('tr').data('supply-id');
                    var initialRemainingQuantity = initialSuppliesDetails[supplyId] ? initialSuppliesDetails[supplyId].initial_remaining_quantity : null;

                    if (checkbox.is(':checked')) {
                        quantityInput.prop('disabled', false).show();
                        if (initialRemainingQuantity === null) {
                            initialSuppliesDetails[supplyId] = {
                                initial_remaining_quantity: parseInt(checkbox.closest('tr').find('.remaining-quantity').text()),
                                selected_quantity: 0
                            };
                        }
                    } else {
                        quantityInput.val(0).prop('disabled', true).hide();
                        updateRemainingQuantity(quantityInput, 0, initialRemainingQuantity);
                    }
                });

                // CẬP NHẬT ĐƠN HÀNG
                $('.editvattu').on('click', function() {
                    var orderId = $(this).data('order-id');
                    var updatedSupplies = [];
                    var removedSupplies = [];
                    var addedSupplies = [];

                    $('#chinhSuaVatTuDonHang .vatTuDonHangChinhSua tbody tr').each(function() {
                        var supplyId = $(this).data('supply-id');
                        var isChecked = $(this).find('.form-check-input').is(':checked');
                        var quantity = $(this).find('.supply-quantity').val();

                        if (isChecked) {
                            updatedSupplies.push({
                                supply_id: supplyId,
                                quantity: quantity
                            });
                        }
                    });

                    // So sánh để tìm ra sự thay đổi
                    initialSupplies.forEach(function(supplyId) {
                        if (!updatedSupplies.some(s => s.supply_id == supplyId)) {
                            removedSupplies.push(supplyId);
                        }
                    });

                    updatedSupplies.forEach(function(supply) {
                        if (!initialSupplies.includes(supply.supply_id)) {
                            addedSupplies.push(supply);
                        }
                    });

                    // Tạo danh sách tên và mã số vật tư đã thay đổi
                    var changesText = '';

                    if (removedSupplies.length > 0) {
                        var removedSuppliesText = removedSupplies.map(function(supplyId) {
                            var supply = initialSuppliesDetails[supplyId];
                            return `${supply.tenvattu} - ${supply.maso}`;
                        }).join('<br>');
                        changesText += `<p>Bạn đã bỏ chọn các vật tư:</p><p>${removedSuppliesText}</p>`;
                    } else {
                        changesText += `<p>Bạn đã bỏ chọn các vật tư:</p><p>Không có vật tư nào</p>`;
                    }

                    if (addedSupplies.length > 0) {
                        var addedSuppliesText = addedSupplies.map(function(supply) {
                            // Lấy tên và mã số vật tư từ phần tử hiện tại trong bảng
                            var row = $(`#chinhSuaVatTuDonHang .vatTuDonHangChinhSua tbody tr[data-supply-id="${supply.supply_id}"]`);
                            var tenvattu = row.find('td:nth-child(3)').text();
                            var maso = row.find('td:nth-child(4)').text();
                            return `${tenvattu} - ${maso}`;
                        }).join('<br>');
                        changesText += `<p>Bạn đã chọn thêm các vật tư:</p><p>${addedSuppliesText}</p>`;
                    } else {
                        changesText += `<p>Bạn đã chọn thêm các vật tư:</p><p>Không có vật tư nào</p>`;
                    }

                    // Hiển thị thông báo xác nhận với SweetAlert2
                    Swal.fire({
                        title: 'Xác nhận thay đổi',
                        html: `
                            ${changesText}
                            <p>Bạn có muốn thay đổi vật tư không?</p>
                        `,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Có, thay đổi',
                        cancelButtonText: 'Không, hủy bỏ'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Người dùng xác nhận thay đổi, thực hiện cập nhật
                            updateOrder(orderId, updatedSupplies, removedSupplies, addedSupplies, changesText);
                        }
                    });
                });

                function updateOrder(orderId, updatedSupplies, removedSupplies, addedSupplies, changesText) {
                    $.ajax({
                        url: '{{ route("capNhatDonHang") }}',
                        type: 'POST',
                        data: {
                            supplies: updatedSupplies,
                            removedSupplies: removedSupplies,
                            addedSupplies: addedSupplies,
                            order_id: orderId,
                            changes_text: changesText
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire(
                                'Thành công!',
                                'Cập nhật thành công!',
                                'success'
                            ).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload(); // Tải lại trang khi người dùng nhấn vào nút xác nhận trên thông báo
                                }
                            });
                            $('#chinhSuaVatTuDonHang').modal('hide');
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Lỗi!',
                                'Có lỗi xảy ra: ' + xhr.responseJSON.error,
                                'error'
                            ).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload(); // Tải lại trang khi người dùng nhấn vào nút xác nhận trên thông báo
                                }
                            });
                        }
                    });
                }

                // Cập nhật số lượng còn lại khi thay đổi số lượng nhập
                $(document).on('input', '.supply-quantity', function() {
                    var quantityInput = $(this);
                    var currentQuantity = parseInt(quantityInput.val());
                    var supplyId = quantityInput.closest('tr').data('supply-id');
                    var initialRemainingQuantity = initialSuppliesDetails[supplyId] ? initialSuppliesDetails[supplyId].initial_remaining_quantity : parseInt(quantityInput.attr('max'));
                    updateRemainingQuantity(quantityInput, currentQuantity, initialRemainingQuantity);
                });

                function updateRemainingQuantity(quantityInput, currentQuantity, initialRemainingQuantity) {
                    var row = quantityInput.closest('tr');
                    var remainingQuantityCell = row.find('.remaining-quantity');
                    var remainingQuantity = initialRemainingQuantity - currentQuantity;
                    remainingQuantityCell.text(remainingQuantity);

                    var checkbox = row.find('.supply-checkbox');
                    if (remainingQuantity < 0) {
                        quantityInput.val(initialRemainingQuantity);
                        remainingQuantityCell.text(0);
                    } else {
                        if (currentQuantity == 0 && !initialSupplies.includes(row.data('supply-id'))) {
                            checkbox.prop('checked', false);
                        }
                    }
                }
            });
        </script>

    {{-- HIỂN THỊ CARD TRÌNH KÝ --}}
        <script>
            $(document).ready(function() {
                $('#printerOrder').click(function() {
                    $('#printCard').css('display', 'flex'); // Sử dụng jQuery để hiển thị card
                });

                // Đoạn mã để ẩn card khi nhấn vào nền xung quanh
                $('#printCard').click(function(e) {
                    if (e.target === this) {
                        $(this).css('display', 'none'); // Ẩn card
                    }
                });
            });
        </script>
    {{-- TÌM KIẾM VẬT TƯ CỦA DANH MỤC --}}
        <script>
            $(document).ready(function() {
                $('#huyDonHang').on('click', function() {
                    // Reloads the current document.
                    location.reload();
                });
            });
        </script>
    {{-- IN BIỂU MẪU KÝ --}}
        <script>
            $(document).ready(function() {
                $('#inTrinhKy').on('click', function() {
                    $('.koIn').hide();
                    $('.donglaimodal').hide();
                    var content = $('#printCardDaKy .card-body').html(); // Lấy nội dung cần in
                    var dataId = $(this).data('id');
                    var status = $(this).data('status');

                    function printContent() {
                        var iframe = document.getElementById('printFrame');
                        iframe.contentWindow.document.open();
                        iframe.contentWindow.document.write(`
                            <html>
                            <head>
                                <title>In Trình Ký</title>
                                <style>
                                    body {
                                        font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
                                        font-size: 10pt;
                                        margin: 0;
                                        padding: 0;
                                        overflow: hidden;
                                    }
                                    table {
                                        width: 100%;
                                        border-collapse: collapse;
                                    }
                                    #tableInTrinhKyDaKy th, #tableInTrinhKyDaKy td {
                                        font-size: 10pt;
                                        border: 1px solid black;
                                        text-align: center;
                                    }
                                    .btn, .donglaimodal, .koin {
                                        display: none;
                                    }
                                </style>
                            </head>
                            <body>${content}</body>
                            </html>`
                        );
                        iframe.contentWindow.document.close();
                        iframe.onload = function() {
                            iframe.contentWindow.print(); // Kích hoạt in
                        };
                    }

                    if (status === 0) {
                        // Chưa in, cần gửi AJAX để cập nhật trạng thái
                        $.ajax({
                            url: '{{ route("luuGiaTriIn") }}',
                            type: 'POST',
                            data: {
                                id: dataId,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                console.log('Data has been sent');
                                printContent(); // In sau khi AJAX thành công
                            },
                            error: function(xhr, status, error) {
                                console.error('An error occurred: ' + error);
                            }
                        });
                    } else {
                        // Đã in, chỉ cần in lại mà không cần gửi AJAX
                        Swal.fire({
                            title: 'Đơn hàng này đã được in',
                            text: 'Bạn có muốn in lại không?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Có, in lại!',
                            cancelButtonText: 'Không'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                printContent(); // Chỉ in lại khi người dùng xác nhận
                            }
                        });
                    }
                });
            });
        </script>
    {{-- HIỂN THỊ VẬT TƯ TRONG ĐƠN HÀNG --}}
        <script>
            $(document).ready(function() {
                //HIỂN THỊ VẬT TƯ CHI TIẾT
                $('.checkThongTin').click(function() {
                    var orderId = $(this).data('order-id');
                    $('input[name="order_id"]').val(orderId);
                    $.ajax({
                        url: "{{ route('DuLieuVatTuChiTiet') }}",
                        type: 'POST',
                        data: {
                            id: orderId,
                            _token: '{{ csrf_token() }}' // Đảm bảo bạn thêm token CSRF
                        },
                        success: function(data) {
                            var tbody = $('#danhMucVatTuChiTiet').find('tbody');
                            tbody.empty(); // Xóa nội dung hiện tại của tbody
                            $('#themvattuchitiet').attr('data-id', orderId);
                            $('#searchVatTuChiTiet').attr('data-id', orderId);
                            var userFunctionId = {{$user->function_id}};
                            var orderName = data.orderName; // Lấy giá trị orderName từ dữ liệu trả về

                            var totalSupplies = data.totalSupplies;
                            var totalDanhan = data.totalDanhan;
                            var totalChuanhan = data.totalChuanhan;
                            var totalDaxuat = data.totalDaxuat;
                            $('#orderTitle').text('Đơn hàng: ' + orderName);
                            $('#tongsovattu').text('Tổng vật tư: ' + totalSupplies);
                            $('#tongdanhan').text('Tổng đã nhận: ' + totalDanhan);
                            $('#tongchuanhan').text('Tổng chưa nhận: ' + totalChuanhan);
                            $('#tongdaxuat').text('Tổng đã xuất: ' + totalDaxuat);
                            $.each(data.supplies, function(index, item) {
                                var selectTenVatTu = $('.tenvattuchitiet').empty().append('<option selected="">Tên vật tư</option>');
                                var selectMaSo = $('.masochitiet').empty().append('<option selected="">Mã số</option>');
                                var selectDonViTinhChiTiet = $('.donvitinhchitiet').empty().append('<option selected="">Đơn vị Tính</option>');
                                var totalNhapKho = item.viewVatTuChiTiet.reduce((sum, vtct) => sum + vtct.soluongnhapkho, 0);
                                var totalDatChatLuong = item.viewVatTuChiTiet.reduce((sum, vtct) => sum + vtct.soluongdatchatluong, 0);

                                // Kiểm tra điều kiện để thêm class blink-warning
                                var rowClass = totalNhapKho > totalDatChatLuong ? 'blink-warning' : '';

                                var donvitinhSet = new Set();
                                $.each(data.supplies, function(index, item) {
                                    selectTenVatTu.append(new Option(item.tenvattu, item.tenvattu));
                                    selectMaSo.append(new Option(item.maso, item.maso));
                                    if (!donvitinhSet.has(item.donvitinh)) {
                                        selectDonViTinhChiTiet.append(new Option(item.donvitinh, item.donvitinh));
                                        donvitinhSet.add(item.donvitinh);
                                    }
                                });

                                if (item) {
                                    console.log(item)
                                    var row = '<tr id="supply-row-' + item.id + '" data-id="' + item.id + '">' +
                                        '<td style="text-align:center;vertical-align: middle">' + (index + 1) + '</td>' +
                                        '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' tenvattu">' + (item.tenvattu) + '</td>' +
                                        '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' maso">' + (item.maso) + '</td>' +
                                        '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' maso">' + (item.maso_new ?? "") + '</td>' +
                                        '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' donvitinh">' + (item.donvitinh) + '</td>' +
                                        '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' soluong">' + (item.soluong) + '</td>' +
                                        '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' soluongnhapkho">' + totalNhapKho + '</td>' +
                                        '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' soluongdatchatluong">' + totalDatChatLuong + '</td>' +
                                        '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' chuanhan">' + (item.chuanhan) + '</td>' +
                                        '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' daxuat">' + (item.daxuat) + '</td>' +
                                        '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' ghichu">' + (item.ghichu !== null ? item.ghichu : '') + '</td>' +
                                        '</tr>';
                                    tbody.append(row);
                                }
                            });
                            // Hiển thị modal
                            $('#danhMucVatTuChiTiet').modal('show');
                        },
                        error: function(error) {
                            console.log(error);
                            alert('Có lỗi xảy ra');
                        }
                    });
                });

                $('#danhMucVatTuChiTiet').on('shown.bs.modal', function () {
                    $('.tenvattuchitiet, .masochitiet').select2({
                        placeholder: "Chọn...",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#danhMucVatTuChiTiet') // Đặt phần tử cha cho dropdown
                    });
                });
            });
        </script>
    {{-- TÌM KIẾM VẬT TƯ CHI TIẾT --}}
        <script>
            $(document).ready(function(){
                $("#searchVatTuChiTiet").click(function() {
                    var tenvattu = $(".tenvattuchitiet").val() === "Tên vật tư" ? "" : $(".tenvattuchitiet").val();
                    var maso = $(".masochitiet").val() === "Mã số" ? "" : $(".masochitiet").val();
                    var donvitinh = $(".donvitinhchitiet").val() === "Đơn vị Tính" ? "" : $(".donvitinhchitiet").val()
                    var orderId = $(this).data('id');
                    $.ajax({
                        url: '{{ route("timkiemvattuchitiet") }}', // Đường dẫn tới route xử lý tìm kiếm
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: '{{ csrf_token() }}', // Đảm bảo thêm token CSRF cho request POST
                            tenvattu: tenvattu,
                            maso: maso,
                            donvitinh: donvitinh,
                            orderId: orderId,
                        },
                        success: function(data) {
                            console.log(data);
                            // Xử lý dữ liệu trả về và hiển thị trong bảng
                            var tbody = $("#danhMucVatTuChiTiet tbody");
                            tbody.empty(); // Xóa nội dung hiện tại của tbody

                            if (data.suppliesDetail && data.suppliesDetail.length > 0) {
                                $.each(data.suppliesDetail, function(index, supplyDetail) {
                                    // Tính tổng số lượng nhập kho và đạt chất lượng
                                    var totalNhapKho = supplyDetail.viewVatTuChiTietData.reduce((sum, vtct) => sum + (vtct.soluongnhapkho || 0), 0);
                                    var totalDatChatLuong = supplyDetail.viewVatTuChiTietData.reduce((sum, vtct) => sum + (vtct.soluongdatchatluong || 0), 0);

                                    // Kiểm tra điều kiện để thêm class blink-warning
                                    var rowClass = totalNhapKho > totalDatChatLuong ? 'blink-warning' : '';

                                    const row = `
                                        <tr id="supply-row-${supplyDetail.supply.id}" data-id="${supplyDetail.supply.id}">
                                            <td style="text-align: center; vertical-align: middle" class="${rowClass}">${index + 1}</td>
                                            <td style="text-align: center; vertical-align: middle" class="${rowClass}">${supplyDetail.supply.tenvattu}</td>
                                            <td style="text-align: center; vertical-align: middle" class="${rowClass}">${supplyDetail.supply.maso}</td>
                                            <td style="text-align: center; vertical-align: middle" class="${rowClass}">${supplyDetail.supply.donvitinh}</td>
                                            <td style="text-align: center; vertical-align: middle" class="${rowClass}">${supplyDetail.supply.soluong}</td>
                                            <td style="text-align: center; vertical-align: middle" class="${rowClass}">${totalNhapKho}</td>
                                            <td style="text-align: center; vertical-align: middle" class="${rowClass}">${totalDatChatLuong}</td>
                                            <td style="text-align: center; vertical-align: middle" class="${rowClass}">${supplyDetail.chuanhan}</td>
                                            <td style="text-align: center; vertical-align: middle" class="${rowClass}">${supplyDetail.daxuat}</td>
                                            <td class="barcode ${rowClass}">${supplyDetail.barcodeHtml || ''}<div>${supplyDetail.supply.maso || ''}</div></td>
                                            <td style="text-align: center; vertical-align: middle" class="${rowClass}">${supplyDetail.supply.note !== null ? supplyDetail.supply.note : ''}</td>
                                        </tr>
                                    `;
                                    tbody.append(row);
                                });
                            } else {
                                // Nếu không có supplies, chỉ hiển thị một hàng thông báo
                                tbody.append(
                                    `<tr>
                                        <td colspan="10" style="text-align:center;vertical-align: middle">Không có dữ liệu</td>
                                    </tr>`
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            alert("Có lỗi xảy ra: " + error);
                        }
                    });
                });
            });
        </script>
    {{-- HIỂN THỊ LỊCH SỬ ĐƠN HÀNG --}}
        <script>
            $(document).ready(function() {
                $('.historyOrder').on('click', function() {
                    var orderId = $(this).data('order-id');

                    $.ajax({
                        url: '{{ route("lichSuOrder") }}',
                        type: 'POST',
                        data: { order_id: orderId },
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success: function(response) {
                            // Hiển thị lịch sử đơn hàng trong modal
                            $('#lichSuOrder .modal-body .table tbody').html(response);
                            $('#lichSuOrder').modal('show');
                        },
                        error: function(xhr) {
                            alert('Error: ' + xhr.responseJSON.error);
                        }
                    });
                });
            });
        </script>


@endsection
