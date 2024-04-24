@extends('Layout.app')
@section('style')
<link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" />
<style>
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
                                    <th>Nội dung</th>
                                    <th>Ghi chú</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $index => $order)
                                    <tr data-order-id="{{$order->id}}">
                                        <td style="text-align: center; vertical-align: middle">{{ $index + 1 }}</td>
                                        <td style="text-align: center; vertical-align: middle">{{ $order->sodonhang }}</td>
                                        <td style="text-align: center; vertical-align: middle">{{ $order->chiphi }}</td>
                                        <td style="text-align: center; vertical-align: middle">{{ $order->noidung }}</td>
                                        <td style="text-align: center; vertical-align: middle">{{ $order->ghichu }}</td>
                                        <td style="text-align: center; vertical-align: middle" class="no-modal-trigger">
                                            <button  class="btn btn-danger xoadonhang" title="Xóa đơn hàng"><i class="ri ri-delete-bin-5-line"></i></button>
                                            <button class="btn btn-secondary suaDonHang" data-order-id="{{$order->id}}" data-bs-target="#chinhSuaVatTuDonHang" data-bs-toggle="modal" title="Sửa đơn hàng"><i class="bi bi-pencil-square"></i></button>
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
                    <div class="filter-box mt-1">
                        <div class="row">
                            <div class="col-6 col-sm-2">
                                <select class="form-select nhaCungCapTimKiem" aria-label="Default select example">
                                    <option value="">Chọn nhà cung cấp</option>
                                    @foreach ($providers as $provider)
                                        @if ($provider->details->isNotEmpty())  <!-- Check if the provider has any details -->
                                            <optgroup label="{{ $provider->name }}">
                                                @foreach ($provider->details as $detail)
                                                    <option value="{{ $detail->name }}">{{ $detail->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 col-sm-2">
                                <button type="submit" id="timkiemDanhMucVatTuChiTiet" class="btn btn-primary" style="margin-left: -5px">Tìm kiếm</button>
                            </div>
                        </div>
                    </div>
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
                        <div class="filter-box mt-1">
                            <div class="row">
                                <div class="col-6 col-sm-2">
                                    <select class="form-select banVeRequest" aria-label="Default select example">>
                                        <option value="X">Yêu cầu xuất bản vẽ</option>
                                        <option value="">Không yêu cầu xuất bản vẽ</option>
                                    </select>
                                </div>
                                <div class="col-6 col-sm-2">
                                    <button type="submit" id="timKiemVatTuChiTietOfDm" class="btn btn-primary" style="margin-left: -5px">Tìm kiếm</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive" style="max-height: 400px;">
                            <table class="table table-borderless table-bordered mt-2 vattuchitietcuadanhmuc" >
                                <thead>
                                    <tr>
                                        <th style="text-align: center; vertical-align: middle;"><input class="form-check-input" id="selectAll" type="checkbox" ></th>
                                        <th style="text-align: center; vertical-align: middle;">STT</th>
                                        <th style="text-align: center; vertical-align: middle;">Tên vật tư</th>
                                        <th style="text-align: center; vertical-align: middle;">Mã số</th>
                                        <th style="text-align: center; vertical-align: middle;">Đơn vị tính</th>
                                        <th style="text-align: center;">Số lượng</th>
                                        <th style="text-align: center;">Yêu cầu xuất bản vẽ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        <td style="text-align: center" colspan="7" scope="col">Vui lòng chọn danh mục tạo đơn hàng</td>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary " id="creatDonHang" style="display: none"><i class="bi bi-cart-plus"></i> Tạo đơn hàng</button>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">trở lại</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- MODAL TẠO THÔNG TIN ĐƠN HÀNG--}}
{{-- MODAL HIỂN THỊ VẬT TƯ CỦA ĐƠN HÀNG--}}
    <div class="modal fade" id="danhMucVatTuChiTiet" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vật tư chi tiết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <button type="submit" id="timkiemVatTuChiTiet" class="btn btn-primary" style="margin-left: -5px">Tìm kiếm</button>
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
                    <div class="table-responsive" style="max-height: 600px;">
                        <table class="table table-bordered table-hover danhmucvattuchitiet">
                            <thead>
                                <tr>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">STT</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Tên vật tư</th>
                                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Mã số</th>
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

                    <button type="button" class="btn btn-outline-primary" id="printerOrder" data-id="">
                        <i class="bi bi-printer"></i> In Đơn hàng
                    </button>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">trở lại</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
{{-- MODAL CHỈNH SỬA VẬT TƯ ĐƠN HÀNG--}}
    <div class="modal fade" id="chinhSuaVatTuDonHang" tabindex="-1" aria-labelledby="vatTuChiTietModalLabel" aria-hidden="true">
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
    </div>
{{-- CARD IN TRÌNH KÝ --}}
    <div id="printCard" class="card" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); align-items: center; justify-content: center; z-index: 9999; padding: 40px;">
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
                                CÔNG TY TNHH MTV SẢN XUẤT XE BUS THACO
                                <br>
                                KCN Cơ Khí Ô tô Chu Lai Trường Hải, Xã Tam Hiệp, Huyện Núi Thành, Tỉnh Quảng Nam, Việt Nam
                                <br>
                                Tel: 02352.22.66.79 - Fax:
                                <br>
                                MST: 4001087522
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
                    <strong style="text-decoration: underline; font-size: 20px">Kính gửi: ...............................</strong>
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
                <div style="text-align: right;">
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
                <button type="button" class="btn btn-success" style="margin-right: 10px;"><i class="bi bi-check-circle"></i> Hoàn thành</button>
                <button type="button" class="btn btn-danger" style="margin-right: 10px;"><i class="bi bi-x-circle"></i> Hủy bỏ</button>
                <button type="button" class="btn btn-primary"><i class="bi bi-save"></i> Lưu</button>
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

            // Bắt sự kiện click vào nút "Tạo đơn hàng" trên từng hàng danh mục
            $(document).on('click', '.tao-don-hang', function() {
                var catalogId = $(this).closest('tr').data('catalog-id'); // Lấy ID của danh mục
                var catalogName = $(this).closest('tr').data('catalog-name');
                var projectName = $(this).closest('tr').data('project-name');
                var kinhgui = $(this).closest('tr').data('kinhgui');
                $('#chiTietVatTu .titleDanhMuc').text('Vật tư danh mục: ' + catalogName); // Cập nhật tiêu đề phần chi tiết
                $('#timKiemVatTuChiTietOfDm').attr('data-catalog-id', catalogId);
                $('#creatDonHang').attr('data-projectName', projectName);
                $('#creatDonHang').attr('data-kinhgui', kinhgui);
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
                                console.log(supply)
                                var noteIcon = supply.note ? `<span class="bi bi-info-circle-fill text-info" style="cursor:pointer;" data-bs-toggle="popover" title="Nguyên nhân" data-bs-content="${supply.note}"></span>` : '';
                                var disabledClass = supply.status === 1 ? 'disabled-row' : '';
                                var checkboxInput = supply.status === 1 ? '' : `<input type="checkbox" class="form-check-input supply-checkbox" value="${supply.id}">`; // Chỉ thêm checkbox nếu status không phải là 1
                                var supplyRow = `<tr class="${disabledClass}"data-id="${supply.id}"
                                        data-tenvattu="${supply.tenvattu}"
                                        data-maso="${supply.maso}"
                                        data-donvitinh="${supply.donvitinh}"
                                        data-soluong="${supply.soluong}"
                                        data-dongia="${supply.dongia || ''}"
                                        data-thanhtien="${supply.thanhtien || ''}"
                                        data-ngayyeucau="${supply.ngayyeucau || ''}"
                                        data-ghichu="${supply.ghichu || ''}">
                                    <td style="text-align: center">${checkboxInput}</td>
                                    <td style="text-align: center">${index + 1}</td>
                                    <td style="text-align: center">${supply.tenvattu} ${noteIcon}</td>
                                    <td style="text-align: center">${supply.maso}</td>
                                    <td style="text-align: center">${supply.donvitinh}</td>
                                    <td style="text-align: center">${supply.soluong}</td>
                                    <td style="text-align: center">${supply.exportdrawings ?? '-'}</td>
                                </tr>`;
                                $('.vattuchitietcuadanhmuc tbody').append(supplyRow);
                            });
                            $('[data-bs-toggle="popover"]').popover();
                        } else {
                            $('.vattuchitietcuadanhmuc tbody').append('<tr><td colspan="6" style="text-align: center">Không có vật tư nào.</td></tr>');
                        }
                        // Kích hoạt chọn tất cả checkboxes khi nhấp vào #selectAll
                        $('#selectAll').click(function() {
                            $('.supply-checkbox').prop('checked', this.checked);
                        });
                    },
                    error: function() {
                        alert("Có lỗi xảy ra, không thể tải dữ liệu vật tư!");
                    }
                });
            });
        });
    </script>
{{-- CẬP NHẬT CHECKBOX --}}
    <script>
        $(document).ready(function() {
            $('#selectAll').click(function() {
                var isChecked = $(this).is(':checked');
                $('.vattuchitietcuadanhmuc tbody input[type="checkbox"]').prop('checked', isChecked);
                toggleCreateOrderButton();
            });

            // Ensure that if not all checkboxes are checked, the main checkbox is unchecked
            $('.vattuchitietcuadanhmuc tbody').on('change', 'input[type="checkbox"]', function() {
                if (!$(this).is(':checked')) {
                    $('#selectAll').prop('checked', false);
                } else {
                    var allChecked = $('.vattuchitietcuadanhmuc tbody input[type="checkbox"]').length === $('.vattuchitietcuadanhmuc tbody input[type="checkbox"]:checked').length;
                    $('#selectAll').prop('checked', allChecked);
                }
                toggleCreateOrderButton();
            });
            function toggleCreateOrderButton() {
                var anyChecked = $('.vattuchitietcuadanhmuc tbody input[type="checkbox"]:checked').length > 0;
                if (anyChecked) {
                    $('#creatDonHang').show();
                } else {
                    $('#creatDonHang').hide();
                }
            }
        });
    </script>
{{-- TẠO ĐƠN HÀNG SAU KHI CHỌN CHECKBOX --}}
    <script>
       $(document).ready(function() {
            $('#creatDonHang').click(function() {
                var projectName = $(this).data('projectname'); // Lấy giá trị data-projectname trực tiếp từ nút
                var kinhGui = $(this).data('kinhgui');
                var selectedSupplies = $('.vattuchitietcuadanhmuc tbody input[type="checkbox"]:checked').map(function() {
                    var row = $(this).closest('tr');
                    return {
                        id: row.find('input[type="checkbox"]').val(),
                        tenvattu: row.find('td').eq(2).text(),
                        maso: row.find('td').eq(3).text(),
                        donvitinh: row.find('td').eq(4).text(),
                        soluong: row.find('td').eq(5).text()
                    };
                }).get();
                if (selectedSupplies.length > 0) {
                    var tbody = $('#tableInTrinhKy tbody');
                    tbody.empty();

                    selectedSupplies.forEach(function(supply, index) {
                        console.log(supply)
                        var newRow = `<tr data-supply-id="${supply.id}">
                            <td style="text-align: center; border: 1px solid black;">${index + 1}</td>
                            <td style="text-align: center; border: 1px solid black;">${supply.maso}</td>
                            <td style="text-align: center; border: 1px solid black;">${supply.tenvattu}</td>
                            <td style="text-align: center; border: 1px solid black;">${supply.donvitinh}</td>
                            <td style="text-align: center; border: 1px solid black;">${supply.soluong}</td>
                            <td style="text-align: center; border: 1px solid black;"><input type="number" class="form-control" tabindex="1" /></td>
                            <td style="text-align: center; border: 1px solid black;"><input type="number" class="form-control" tabindex="2" /></td>
                            ${index === 0 ? `<td rowspan="${selectedSupplies.length}" style="text-align: center; border: 1px solid black;"><input type="date" class="form-control" /></td>` : ''}
                            ${index === 0 ? `<td rowspan="${selectedSupplies.length}" style="text-align: center; border: 1px solid black;">Dự án:${projectName}</td>` : ''}
                        </tr>`;
                        tbody.append(newRow);
                    });
                    $("#kinhGuiCard").html(`<strong style="text-decoration: underline; font-size: 20px">Kính gửi: ${kinhGui}</strong>`);
                    // Hiển thị #printCard và ẩn modal #vautucuadanhmuc
                    $('#printCard').css('display', 'flex');
                    $('#vautucuadanhmuc').modal('hide');  // Ẩn modal danh mục vật tư
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Chú ý!',
                        text: 'Vui lòng chọn ít nhất một vật tư để tạo đơn hàng.',
                        confirmButtonText: 'Đóng'
                    });
                }
            });
        });

    </script>

 {{-- url: '{{ route("taoDonHangMoi") }}', --}}


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
{{-- HIỂN THỊ MODAL VẬT TƯ CHI TIẾT --}}
    <script>
        $(document).ready(function() {
            $('.vattuchitietdonhang tbody tr').click(function() {
                    if ($(event.target).closest('.no-modal-trigger').length) {
                        // Nếu có, không làm gì cả để ngăn chặn hiển thị modal
                        return;
                    }
                    var orderId = $(this).data('order-id');
                    // alert(orderId)
                    $.ajax({
                        url: "{{ route('DuLieuVatTuChiTiet') }}",
                        type: 'POST',
                        data: {
                            id: orderId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {

                            var tbody = $('#danhMucVatTuChiTiet').find('tbody');
                            tbody.empty();
                            $('#themvattuchitiet').attr('data-id', orderId);
                            $('#timkiemVatTuChiTiet').attr('data-id', orderId);
                            var orderName = data.orderName;
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

                                        var row = '<tr id="supply-row-' + item.id + '"  data-id="' + item.id + '">' +
                                        '<td  style="text-align:center;vertical-align: middle">' + (index + 1) + '</td>' +
                                        '<td  style="text-align:center;vertical-align: middle" class="' + rowClass + ' tenvattu">' + (item.tenvattu) + '</td>' +
                                        '<td  style="text-align:center;vertical-align: middle" class="' + rowClass + ' maso">' + (item.maso) + '</td>' +
                                        '<td  style="text-align:center;vertical-align: middle" class="' + rowClass + ' donvitinh">' + (item.donvitinh) + '</td>' +
                                        '<td  style="text-align:center;vertical-align: middle" class="' + rowClass + ' soluong">' + (item.soluong) + '</td>' +
                                        '<td  style="text-align:center;vertical-align: middle" class="' + rowClass + ' soluongnhapkho">' + totalNhapKho + '</td>' +
                                        '<td  style="text-align:center;vertical-align: middle" class="' + rowClass + ' soluongdatchatluong">' + totalDatChatLuong + '</td>' +
                                        '<td  style="text-align:center;vertical-align: middle" class="' + rowClass + ' chuanhan">' + (item.chuanhan) + '</td>' +
                                        '<td  style="text-align:center;vertical-align: middle" class="' + rowClass + ' daxuat">' + (item.daxuat) + '</td>' +
                                        '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' ghichu">' + (item.ghichu !== null ? item.ghichu : '') + '</td>' +
                                        '</tr>';
                                    tbody.append(row);
                                }
                            });
                            // Hiển thị modal
                            $('#danhMucVatTuChiTiet').modal('show');
                        },
                        error: function(error) {

                        }
                    });
                });
                $('#danhMucVatTuChiTiet').on('shown.bs.modal', function () {
                    $('.tenvattuchitiet, .masochitiet').select2({
                        placeholder: "Chọn...",
                        allowClear: true,
                        width: '100%',
                        dropdownParent: $('#danhMucVatTuChiTiet')
                    });
                });
        });
    </script>
{{-- SỬA ĐƠN HÀNG --}}
    <script>
        $(document).ready(function() {
            $('.suaDonHang').on('click', function() {
                var orderId = $(this).data('order-id'); // Lấy ID của đơn hàng từ data attribute
                $('.editvattu').data('order-id', orderId);
                $.ajax({
                    url: '{{ route("suaDonHang") }}',
                    type: 'POST',
                    data: { order_id: orderId },
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if (response.error) {
                            alert(response.error);
                            return;
                        }
                        var tbody = $('#chinhSuaVatTuDonHang .vatTuDonHangChinhSua tbody');
                        tbody.empty(); // Xóa các hàng hiện tại trong bảng
                        $.each(response.supplies, function(index, supply) {
                            tbody.append(`
                                <tr data-supply-id="${supply.id}">
                                    <td style="text-align: center; vertical-align: middle;"><input class="form-check-input" type="checkbox" ${supply.order_id === orderId ? 'checked' : ''}></td>
                                    <td style="text-align: center; vertical-align: middle;">${index + 1}</td>
                                    <td style="text-align: center; vertical-align: middle;">${supply.tenvattu}</td>
                                    <td style="text-align: center; vertical-align: middle;">${supply.maso}</td>
                                    <td style="text-align: center; vertical-align: middle;">${supply.donvitinh}</td>
                                    <td style="text-align: center;">${supply.soluong}</td>
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
        });
    </script>
{{-- CẬP NHẬT ĐƠN HÀNG --}}
    <script>
        $(document).ready(function() {
            $('.editvattu').on('click', function() {
                var orderId = $(this).data('order-id');
                var updatedSupplies = [];
                $('#chinhSuaVatTuDonHang .vatTuDonHangChinhSua tbody tr').each(function() {
                    var supplyId = $(this).data('supply-id');  // Giả sử mỗi hàng có attribute này chứa ID của vật tư
                    var isChecked = $(this).find('.form-check-input').is(':checked');
                    updatedSupplies.push({
                        supply_id: supplyId,
                        selected: isChecked,
                        orderId: orderId
                    });
                });

                $.ajax({
                    url: '{{ route("capNhatDonHang") }}',
                    type: 'POST',
                    data: {
                        supplies: updatedSupplies
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
            });
        });
    </script>
{{-- TÌM KIẾM DANH MỤC VẬT TƯ --}}
    <script>
        $(document).ready(function() {
            function bindOrderButtonEvents() {
                // Bắt sự kiện click vào nút "Tạo đơn hàng" trên từng hàng danh mục
                $(document).on('click', '.tao-don-hang', function() {
                    var catalogId = $(this).closest('tr').data('catalog-id');
                    var catalogName = $(this).closest('tr').data('catalog-name');
                    var nhacungcap = $(this).data('nhacungcap');
                    $('#supplier').val(nhacungcap);
                    $('#chiTietVatTu .titleDanhMuc').text('Vật tư danh mục: ' + catalogName);

                    $.ajax({
                        type: 'POST',
                        url: '{{ route("duLieuVatTuCuaDanhMuc") }}',
                        data: {
                            id: catalogId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(data) {
                            updateDetailsTable(data);
                        },
                        error: function() {
                            alert("Có lỗi xảy ra, không thể tải dữ liệu vật tư!");
                        }
                    });
                });
            }

            function updateDetailsTable(data) {
                var tbody = $('.vattuchitietcuadanhmuc tbody');
                tbody.empty();
                if (data.supplies.length > 0) {
                    data.supplies.forEach(function(supply, index) {
                        var newRow = createSupplyRow(supply, index);
                        tbody.append(newRow);
                    });
                    $('[data-bs-toggle="popover"]').popover();
                } else {
                    tbody.append('<tr><td colspan="7" style="text-align: center">Không có vật tư nào.</td></tr>');
                }
            }

            function createSupplyRow(supply, index) {
                console.log(supply)
                var noteIcon = supply.note ? `<span class="bi bi-info-circle-fill text-info" style="cursor:pointer;" data-bs-toggle="popover" title="Nguyên nhân" data-bs-content="${supply.note}"></span>` : '';
                var disabledClass = supply.status === 1 ? 'disabled-row' : '';
                var checkboxInput = supply.status === 1 ? '' : `<input type="checkbox" class="form-check-input supply-checkbox" value="${supply.id}">`;
                return `<tr class="${disabledClass}">
                            <td style="text-align: center">${checkboxInput}</td>
                            <td style="text-align: center">${index + 1}</td>
                            <td style="text-align: center">${supply.tenvattu} ${noteIcon}</td>
                            <td style="text-align: center">${supply.maso}</td>
                            <td style="text-align: center">${supply.donvitinh}</td>
                            <td style="text-align: center">${supply.soluong}</td>
                            <td style="text-align: center">${supply.exportdrawings ?? '-'}</td>
                        </tr>`;
            }

            bindOrderButtonEvents(); // Gọi hàm này để bắt sự kiện click cho nút tạo đơn hàng

            $('#timkiemDanhMucVatTuChiTiet').click(function() {
                var selectedProvider = $('.nhaCungCapTimKiem').val();
                $.ajax({
                    url: "{{route('timKiemDanhMucVatTu')}}",
                    type: 'POST',
                    data: {
                        nhaCungCap: selectedProvider,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        console.log(response)
                        updateCatalogTable(response); // Cập nhật bảng danh mục
                        bindOrderButtonEvents(); // Tái gắn sự kiện cho các nút mới được thêm vào
                    },
                    error: function(xhr) {
                        console.error('Error: ' + xhr.statusText);
                    }
                });
            });
        });

        function updateCatalogTable(data) {
            var tbody = $('.danhmucvattulist tbody');
            tbody.empty();
            if (data && data.length) {
                data.forEach(function(item, index) {

                    var newRow = `<tr data-catalog-id="${item.id}" data-catalog-name="${item.name}">
                                    <td style="text-align: center">${index + 1}</td>
                                    <td style="text-align: center">${item.project.segment.brand.name}</td>
                                    <td style="text-align: center">${item.project.name}</td>
                                    <td style="text-align: center">${item.name}</td>
                                    <td style="text-align: center">${item.nhacungcap}</td>
                                    <td style="text-align: center">${item.description || 'Không có mô tả'}</td>
                                    <td style="text-align: center"><button type="button" data-nhacungcap="${item.nhacungcap}" class="btn btn-secondary tao-don-hang" title="Thông tin danh mục"><i class="bi bi-cart4"></i></button></td>
                                </tr>`;
                    tbody.append(newRow);
                });
            } else {
                tbody.append('<tr><td colspan="7" style="text-align: center">Không có dữ liệu</td></tr>');
            }
        }
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
            $('#timKiemVatTuChiTietOfDm').on('click', function() {
                var catalogId = $(this).data('catalog-id');  // Lấy giá trị của data-catalog-id
                var banVeRequest = $('.banVeRequest').val();

                if (!catalogId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Bạn phải chọn danh mục trước khi tìm kiếm vật tư!',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    });
                } else {
                    $.ajax({
                        url: "{{ route('timKiemVatTuOfDM') }}",
                        type: 'POST',
                        data: {
                            catalogId: catalogId,
                            banVeRequest: banVeRequest,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $('.vattuchitietcuadanhmuc tbody').empty();
                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(supply, index) {

                                    var noteIcon = supply.note ? `<span class="bi bi-info-circle-fill text-info" style="cursor:pointer;" data-bs-toggle="popover" title="Ghi chú" data-bs-content="${supply.note}"></span>` : '';
                                    var disabledClass = supply.status === 1 ? 'disabled-row' : '';
                                    var checkboxInput = supply.status === 1 ? '' : `<input type="checkbox" class="form-check-input supply-checkbox" value="${supply.id}">`;
                                    var supplyRow = `<tr class="${disabledClass}">
                                        <td style="text-align: center">${checkboxInput}</td>
                                        <td style="text-align: center">${index + 1}</td>
                                        <td style="text-align: center">${supply.tenvattu} ${noteIcon}</td>
                                        <td style="text-align: center">${supply.maso}</td>
                                        <td style="text-align: center">${supply.donvitinh}</td>
                                        <td style="text-align: center">${supply.soluong}</td>
                                        <td style="text-align: center">${supply.exportdrawings ?? ''}</td>
                                    </tr>`;
                                    $('.vattuchitietcuadanhmuc tbody').append(supplyRow);
                                });
                                $('[data-bs-toggle="popover"]').popover();
                            } else {
                                $('.vattuchitietcuadanhmuc tbody').append('<tr><td colspan="7" style="text-align: center">Không có vật tư nào.</td></tr>');
                            }
                            $('#selectAll').click(function() {
                                $('.supply-checkbox').prop('checked', this.checked);
                            });
                        },
                        error: function(xhr) {
                            console.error('Error: ' + xhr.statusText);
                        }
                    });
                }
            });
        });


    </script>
@endsection
