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
  </style>
  <style>
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
    Nhập kho
@endsection

@section('content')
<div class="pagetitle">
    <h1>Danh sách đơn hàng</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Trang chủ</a></li>
            <li class="breadcrumb-item">{{ $module }}</li>
            <li class="breadcrumb-item"><a href="{{route('listBrand', ['module' => $module])}}">Thương hiệu</a></li>
            <li class="breadcrumb-item"><a href="{{ route('listProject', [$segmentId,'module' => $module]) }}">Dự án</a></li>
            <li class="breadcrumb-item active">Đơn hàng</li>
        </ol>
    </nav>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mt-2" style="font-size: 18px;font-weight: 600;color: #012970;">
                      <span style="font-size: 18px;font-weight: 600;color: #012970;">Thương hiệu: <span style="color: black">{{ $brandName }}</span> |
                      <span style="font-size: 18px;font-weight: 600;color: #012970;">Phân khúc: <span style="color: black">{{ $segmentName }}</span> |
                      <span style="font-size: 18px;font-weight: 600;color: #012970;">Dự án: <span  style="color: black">{{ $project->name }}</span> |
                      <span style="font-size: 18px;font-weight: 600;color: #012970;">Tổng số vật tư: <span  style="color: black">{{ $totalSuppliesForProject ?? 0 }}</span> |
                      <span style="font-size: 18px;font-weight: 600;color: #012970;">Tổng Đã nhận: <span  style="color: black">{{ $totalOrdersDanhan ?? 0 }}</span>  |
                      <span style="font-size: 18px;font-weight: 600;color: #012970;">Tổng vật tư chưa nhận: <span  style="color: black">{{ $totalOrdersChuanhan ?? 0 }}</span> |
                      <span style="font-size: 18px;font-weight: 600;color: #012970;">Tổng vật tư đã xuất: <span  style="color: black">{{ $totalOrdersDaxuat ?? 0 }}</span>
                    </h5>
                      <button type="button" class="btn btn-outline-primary ri-search-line timkiemdonhang" data-projectId="{{$project->id}}" data-bs-toggle="modal" data-bs-target="#Timkiemvattu"> Tìm kiếm</button>
                        @if ($user->department_id==3)
                            <a href="{{ route('checkQuality', ['id' => $project->id]) }}" class="btn btn-outline-primary bi bi-journal-check"> Kiểm tra chất lượng</a>
                        @endif
                      <!-- Nội dung thông báo thành công -->
                          @if(session('success'))
                              <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show mt-3" role="alert">
                                  {{ session('success') }}
                                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>
                          @elseif(session('error'))
                              <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show mt-3" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>
                          @endif
                          <div class="table-responsive">
                            <table class="table table-borderless table-bordered table-hover mt-2">
                                <thead>
                                        <tr>
                                            <th style="text-align: center" rowspan="2" scope="col">Stt</th>
                                            <th style="text-align: center" rowspan="2" scope="col">Số đơn hàng</th>
                                            <th style="text-align: center" rowspan="2" scope="col">NCC</th>
                                            <th style="text-align: center" rowspan="2" scope="col">Nội dung</th>
                                            <th style="text-align: center" colspan="4" scope="col">Tình trạng</th>
                                            <th style="text-align: center" rowspan="2" scope="col">Chi phí</th>
                                            <th style="text-align: center" rowspan="2" scope="col">Ghi chú</th>
                                        </tr>
                                      <tr>

                                            <th style="text-align: center" scope="col">Tổng</th>
                                            <th style="text-align: center" scope="col">Đã nhận</th>
                                            <th style="text-align: center" scope="col">Chưa nhận</th>
                                            <th style="text-align: center" scope="col">Đã xuất</th>
                                      </tr>
                                </thead>
                                <tbody>
                                  @php
                                    $stt = 1;
                                  @endphp
                                    @forelse ($orders as $index => $order)
                                        <tr data-id="{{ $order->id }}">
                                            <td class="no-modal-trigger" style="text-align: center;vertical-align: middle;">{{ $stt++ }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->sodonhang }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->nhacungcap }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->noidung }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->total_supplies ?? '0' }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->total_danhan ?? '0' }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->total_chuanhan ?? '0' }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->total_daxuat ?? '0' }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->chiphi }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->ghichu }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="16" style="text-align: center;vertical-align: middle;">Không có dữ liệu</td>
                                        </tr>
                                    @endforelse
                              </tbody>
                            </table>
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
              <form action="{{ route('listNhapKho', ['project' => $project->id]) }}" method="GET">
              <div class="modal-body">
                <input type="text" value="{{$module}}" name="module" hidden>
                  <input type="text" value="{{$project->id}}" name="project_id" hidden>
                    <div class="col-sm-12">
                        <div class="form-check d-flex align-items-center mt-2">
                            <!-- Checkbox -->
                            <div class="form-check col-sm-3">
                                <input class="form-check-input" type="checkbox" id="sodonhangCheckbox" >
                                <label class="form-check-label" for="sodonhang">
                                    Số đơn hàng:
                                </label>
                            </div>
                            @csrf
                            <!-- Select menu -->
                            <select class="form-select ml-auto" id="sodonhang" name="sodonhangSelect" aria-label="Default select example" required disabled>
                                <option value="">Chọn đơn hàng</option>
                            </select>
                        </div>

                        <div class="form-check d-flex align-items-center mt-2">
                            <!-- Checkbox -->
                            <div class="form-check col-sm-3">
                            <input class="form-check-input" type="checkbox" id="nhacungcapCheckbox" >
                            <label class="form-check-label" for="nhacungcap">
                                Nhà cung cấp:
                            </label>
                            </div>
                            <select class="form-select ml-auto" id="nhacungcap" name="nhacungcapSelect" aria-label="Default select example" required disabled>
                                <option value="">Chọn nhà cung cấp</option>
                            </select>
                        </div>
                        <div class="form-check d-flex align-items-center mt-2">
                            <!-- Checkbox -->
                            <div class="form-check col-sm-3">
                                <input class="form-check-input" type="checkbox" id="tinhtrangCheckbox" >
                                <label class="form-check-label" for="tinhtrang">
                                    Tình trạng:
                                </label>
                            </div>

                            <select class="form-select ml-auto" id="nhacungcapSuppelies" name="nhacungcapSuppeliesSelect" aria-label="Default select example" required disabled>
                                <option value="">Chọn tình trạng</option>
                                <option value="Đã nhận">Đã nhận</option>
                                <option value="Chưa nhận">Chưa nhận</option>
                                <option value="Đã xuất">Đã xuất</option>
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
{{-- MODAL DANH MỤC VẬT TƯ--}}
    <div class="modal fade" id="danhMucVatTuChiTiet" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Vật tư chi tiết</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <form class="modal-body quetBarcodeNhapKho" action="{{ route('quetBarcodeNhapKho') }}" method="get">
                        @csrf
                        <div class="filter-box mt-3">
                            <div class="row">
                                <div class="col-6 col-md-2">
                                    <select class="form-select tenvattuchitiet" aria-label="Default select example">
                                        <option selected="">Tên vật tư</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-2">
                                    <select class="form-select masochitiet" aria-label="Default select example">
                                        <option selected="">Mã số</option>
                                    </select>
                                </div>
                                <div class="col-6 col-md-2">
                                    <select class="form-select donvitinhchitiet" aria-label="Default select example">
                                        <option selected="">Chọn đơn vị tính</option>
                                    </select>
                                </div>
                                <div class="col-12 col-md-2">
                                    <a id="timkiemVatTuChiTiet" class="btn btn-primary btn-block">Tìm kiếm</a>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap align-items-center mt-2">
                            <h6 class="modal-title" id="orderTitle">Đơn hàng:</h6>
                            <span>|</span>
                            <h6 class="modal-title" id="tongsovattu">Tổng vật tư:</h6>
                            <span>|</span>
                            <h6 class="modal-title" id="tongdanhan">Tổng đã nhận:</h6>
                            <span>|</span>
                            <h6 class="modal-title" id="tongchuanhan">Tổng chưa nhận:</h6>
                            <span>|</span>
                            <h6 class="modal-title" id="tongdaxuat">Tổng đã xuất:</h6>
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
                                            <th rowspan="2" style="text-align: center; vertical-align: middle;">Mã Boardcode</th>
                                            <th rowspan="2" style="text-align: center; vertical-align: middle;">Ghi chú</th>
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
                            @if ($user->department_id!=3)
                                <a  class="btn btn-outline-primary mt-2" id="chonvattu"><i class="bi bi-folder-plus"></i> Chọn vật tư</a>
                            @endif
                            <button id="inVatTu" type="button" class="btn btn-outline-primary mt-2" style="display: none"><i class="bi bi-printer"></i>In mã barcode</button>
                            <button id="nhapKho" type="submit" class="btn btn-outline-primary mt-2" style="display: none"><i class="bi bi-box-arrow-in-down"></i> Nhập kho</button>
                    </form>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở lại</button>
                </div>
            </div>
        </div>
    </div>
{{-- LỊCH SỬ GIAO DỊCH VẬT TƯ --}}
    <div class="modal fade" style="background-color: #000000bb" id="lichsugiaodich" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Lịch sử giao dịch</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="text-align: center; vertical-align: middle;">STT</th>
                                <th style="text-align: center; vertical-align: middle;">Tên vật tư</th>
                                <th style="text-align: center; vertical-align: middle;">Mã số</th>
                                <th style="text-align: center; vertical-align: middle;">Loại giao dịch</th>
                                <th style="text-align: center; vertical-align: middle;">Số lượng</th>
                                <th style="text-align: center;">Ngày giao dịch</th>
                                <th style="text-align: center; vertical-align: middle;">Ghi chú</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

{{-- IN DANH SÁCH VẬT TƯ --}}
@endsection

@section('script')
<script src="{{asset('assets/js/select2.min.js')}}"></script>
  <script>
    $('.btn-secondary').click(function() {
        var supplyId = $(this).data('id'); // Lấy ID từ data-id của nút
        $('#supplyId').val(supplyId); // Đặt ID vào trường ẩn
    });
  </script>
  {{-- HIỂN THỊ MODAL VẬT TƯ CHI TIẾT --}}
    <script>
            $(document).ready(function() {
                $('.table-hover tbody tr').click(function() {
                        if ($(event.target).closest('.no-modal-trigger').length) {
                            // Nếu có, không làm gì cả để ngăn chặn hiển thị modal
                            return;
                        }
                        var orderId = $(this).data('id');
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
                                            '<td class="barcode ' + rowClass + '">' + (item.barcodeHtml || '') + '<div>' + (item.maso || '') + '</div></td>' +
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
                            dropdownParent: $('#danhMucVatTuChiTiet')
                        });
                    });
            });
    </script>
  {{-- THÊM VẬT TƯ CHI TIẾT --}}
    <script>
        $(document).ready(function() {
            var order_id
            $('#themvattuchitiet').click(function() {
                order_id = $(this).data('id');
                $('#themvattuthucong').modal('show');
            });

            $('#themvattu').click(function() {
                // Thu thập dữ liệu từ modal
                var tenvattu = $('input[name="tenvattu"]').val();
                var maso = $('input[name="maso"]').val();
                var donvitinh = $('select[name="donvitinh"]').val();
                var soluong = $('input[name="soluongvattuthemvao"]').val();
                $.ajax({
                    url: "{{ route('themvattuchitiet') }}",
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
                        tenvattu: tenvattu,
                        maso: maso,
                        donvitinh: donvitinh,
                        soluong: soluong,
                        order_id: order_id,
                    },
                    success: function(data) {
                        if (!data.success) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Không thể thêm vật tư',
                                    text: data.message, // Hiển thị thông báo lỗi từ server
                                });
                            } else {
                            var tbody = $('#danhMucVatTuChiTiet').find('tbody');
                            tbody.empty(); // Xóa nội dung hiện tại của tbody
                            $.each(data.supplies, function(index, item) {
                                var buttonsHtml = `<td style="text-align:center;vertical-align: middle">
                                    <button class="btn btn-sm btn-primary chinhsuavattuchitiet"
                                        data-bs-toggle="modal"
                                        data-orderId="${order_id}"
                                        data-tenvattu="${item.tenvattu}"
                                        data-maso="${item.maso}"
                                        data-donvitinh="${item.donvitinh}"
                                        data-soluong="${item.soluong}">Sửa</button>
                                    <button class="btn btn-sm btn-danger xoavattuchitiet"
                                        data-id="${item.id}">Xóa</button>
                                    </td>`;

                                var row = '<tr>' +
                                    '<td style="text-align:center;vertical-align: middle">' + (index + 1) + '</td>' +
                                    '<td style="text-align:center;vertical-align: middle">' + (item.tenvattu) + '</td>' +
                                    '<td style="text-align:center;vertical-align: middle">' + (item.maso) + '</td>' +
                                    '<td style="text-align:center;vertical-align: middle">' + (item.donvitinh) + '</td>' +
                                    '<td style="text-align:center;vertical-align: middle">' + (item.soluong) + '</td>' +
                                    '<td style="text-align:center;vertical-align: middle">' + (item.danhan) + '</td>' +
                                    '<td style="text-align:center;vertical-align: middle">' + (item.chuanhan) + '</td>' +
                                    '<td style="text-align:center;vertical-align: middle">' + (item.daxuat) + '</td>' +
                                    '<td>' + (item.barcodeHtml || '') + '<div>' + (item.maso || '') + '</div></td>' +
                                    buttonsHtml +
                                    '</tr>';
                                tbody.append(row);
                            });
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: 'Vật tư đã được thêm thành công.',
                            });
                        }
                    },

                    error: function(xhr) {
                        alert('Có lỗi xảy ra, không thể thêm vật tư');
                    }
                });
            });
        });
    </script>
  {{-- HIỂN THỊ LỊCH SỬ --}}
    <script>
        $('.danhmucvattuchitiet tbody').on('click', 'tr', function(event) {
            if ($(event.target).closest('.stt-checkbox').length) {
                return;
            }
            if ($(event.target).closest('.action-btn').length) {
                return;
            }
            if ($(event.target).is('input')) {
                return;
            }
            var supplyId = $(this).data('id'); // Lấy id của vật tư

            $.ajax({
                url:"{{ route('lichsuvattu') }}", // Thay đổi thành URL thực tế của bạn
                type: 'POST',
                data: {
                    id: supplyId, // Gửi ID của vật tư
                    _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                },
                success: function(response) {
                    var htmlContent = '';

                    if (response.message || response.length === 0) {
                        // Nếu server trả về thông báo không có giao dịch hoặc mảng trả về rỗng
                        htmlContent = '<tr><td colspan="9" class="text-center">Chưa có giao dịch cho vật tư này.</td></tr>';
                    } else {
                        // Nếu có dữ liệu giao dịch, xây dựng bảng
                        response.forEach(function(transaction, index) {

                            htmlContent += `<tr>
                                                <td style="text-align: center; vertical-align: middle;">${index + 1}</td>
                                                <td style="text-align: center; vertical-align: middle;">${transaction.supply.tenvattu}</td>
                                                <td style="text-align: center; vertical-align: middle;">${transaction.supply.maso}</td>
                                                <td style="text-align: center; vertical-align: middle;">${transaction.loaigiaodich}</td>
                                                <td style="text-align: center; vertical-align: middle;">${transaction.soluong}</td>
                                                <td style="text-align: center; vertical-align: middle;">${transaction.ngaygiaodich}</td>
                                                <td style="text-align: center; vertical-align: middle;">${transaction.ghichu ? transaction.ghichu : ''}</td>
                                            </tr>`;
                        });
                    }

                    // Đặt htmlContent vào tbody của bảng trong modal
                    $('#lichsugiaodich .modal-body .table tbody').html(htmlContent);

                    // Hiển thị modal
                    $('#lichsugiaodich').modal('show');
                },
                error: function(error) {
                    console.log(error);
                    // Hiển thị thông báo lỗi
                    $('#lichsugiaodich .modal-body').html('<p>Có lỗi xảy ra khi tải dữ liệu.</p>');
                    $('#lichsugiaodich').modal('show');
                }
            });
        });

    </script>
  {{-- TÌM KIẾM ĐƠN HÀNG --}}
    <script>
        $(document).ready(function(){
            $('.timkiemdonhang').click(function(){
                var projectId = $(this).data('projectid');

                $.ajax({
                    url: '{{ route("soDonHangvaNCC") }}',
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        project_id: projectId,
                    },
                    success: function(response) {
                        $('#sodonhang').find('option:not(:first)').remove();
                        $('#nhacungcap').find('option:not(:first)').remove();
                        $.each(response.orders, function(index, order) {
                            $('#sodonhang').append($('<option>', {
                                value: order.sodonhang,
                                text: order.sodonhang
                            }));
                            if ($('#nhacungcap option[value="' + order.nhacungcap + '"]').length == 0) {
                                $('#nhacungcap').append($('<option>', {
                                    value: order.nhacungcap,
                                    text: order.nhacungcap
                                }));
                            }
                        });
                    },
                    error: function(error){
                        // Xử lý khi có lỗi
                        console.log(error);
                    }
                });
            });
        });
        $(document).ready(function(){
            // Sự kiện khi checkbox số đơn hàng thay đổi
            $('#sodonhangCheckbox').change(function(){
                if(this.checked) {
                    $('#sodonhang').removeAttr('disabled');
                } else {
                    $('#sodonhang').attr('disabled', 'disabled');
                }
            });

            // Sự kiện khi checkbox nhà cung cấp thay đổi
            $('#nhacungcapCheckbox').change(function(){
                if(this.checked) {
                    $('#nhacungcap').removeAttr('disabled');
                } else {
                    $('#nhacungcap').attr('disabled', 'disabled');
                }
            });

            // Sự kiện khi checkbox tình trạng thay đổi
            $('#tinhtrangCheckbox').change(function(){
                if(this.checked) {
                    $('#nhacungcapSuppelies').removeAttr('disabled');
                } else {
                    $('#nhacungcapSuppelies').attr('disabled', 'disabled');
                }
            });
        });
    </script>
  {{-- HIỂN THỊ CHECKBOX ĐỂ CHỌN VẬT TƯ --}}
    <script>
        $(document).ready(function() {
            var isCheckboxAdded = false;

            $('#chonvattu').click(function() {
                if (!isCheckboxAdded) {
                    // Thêm checkbox vào bảng và thay đổi nút "Chọn Vật Tư" thành "Trở Về"
                    $('.danhmucvattuchitiet tbody tr').each(function() {
                        var vattuId = $(this).data('id');
                        var checkboxHtml = '<input type="checkbox" class="form-check-input stt-checkbox" name="selectedItems[]" value="' + vattuId + '">';
                        $(this).find('td:first').html(checkboxHtml);
                    });

                    // Tạo và thêm checkbox "Chọn Tất Cả" vào tiêu đề cột STT
                    var headerCheckboxHtml = '<input type="checkbox" class="form-check-input" id="selectAll">';
                    $('.danhmucvattuchitiet thead th:first').html(headerCheckboxHtml);

                    isCheckboxAdded = true;
                    $(this).html('<i class="bi bi-arrow-left"></i> Trở Về');
                } else {
                    // Khôi phục bảng và nút "Chọn Vật Tư"
                    // Loại bỏ các input bằng cách khôi phục nội dung gốc của các ô
                    $('.danhmucvattuchitiet tbody tr').each(function(index) {
                        $(this).find('td').each(function() {
                            // Lấy nội dung gốc từ data attribute và khôi phục nó
                            var originalContent = $(this).data('original-content');
                            if (originalContent !== undefined) {
                                $(this).html(originalContent);
                            }
                        });
                        // Khôi phục lại số thứ tự ban đầu cho mỗi hàng ở cột đầu tiên
                        $(this).find('td:first').html(index + 1);
                    });

                    // Khôi phục tiêu đề cột STT và loại bỏ checkbox "Chọn Tất Cả"
                    $('.danhmucvattuchitiet thead th:first').html('STT');
                    $('#selectAll').prop('checked', false).parent().html('STT'); // Giả định rằng checkbox "Chọn Tất Cả" nằm trong thẻ <th>

                    $('#inVatTu, #nhapKho').hide();
                    isCheckboxAdded = false;
                    $(this).html('<i class="bi bi-folder-plus"></i> Chọn Vật Tư');
                }
            });

            // Sự kiện khi giá trị của bất kỳ checkbox nào thay đổi, bao gồm "Chọn Tất Cả"
            $(document).on('change', '.stt-checkbox', function() {
                var $row = $(this).closest('tr');
                var soluong = parseInt($row.find('.soluong').text().trim(), 10); // Số lượng có sẵn
                var totalNhapKho = parseInt($row.find('.soluongnhapkho').data('original-content') || 0, 10); // Số lượng đã nhập, từ dữ liệu lưu trữ

                var $soluongnhapkhoCell = $row.find('.soluongnhapkho');

                if ($(this).is(':checked')) {
                    // Lưu giá trị hiện tại trước khi thay thế bằng input
                    $soluongnhapkhoCell.data('original-content', totalNhapKho);

                    // Giới hạn số lượng nhập không được vượt quá soluong - totalNhapKho
                    var maxQty = soluong - totalNhapKho;

                    // Thay đổi nội dung của cột 'soluongnhapkho' bằng input mới với giới hạn max
                    var inputHtml = `<input type="number" class="form-control soluongnhapkho-input" value="0" min="0" max="${maxQty}" />`;
                    $soluongnhapkhoCell.html(inputHtml);
                } else {
                    // Nếu checkbox bị bỏ chọn, khôi phục giá trị ban đầu
                    var originalContent = $soluongnhapkhoCell.data('original-content');
                    $soluongnhapkhoCell.html(originalContent);
                }
                checkInputsAndToggleButtons();
            });

            // Thêm sự kiện kiểm tra giá trị nhập vào input
            $(document).on('input', '.soluongnhapkho-input', function() {
                var maxQty = parseInt($(this).attr('max'), 10);
                var currentValue = parseInt($(this).val(), 10);

                // Điều chỉnh giá trị nếu nó vượt quá giới hạn max
                if (currentValue > maxQty) {
                    $(this).val(maxQty);
                } else if (currentValue < 0) {
                    // Hoặc nếu người dùng nhập giá trị âm
                    $(this).val(0);
                }
                checkInputsAndToggleButtons();
            });



            // Sự kiện cho checkbox "Chọn Tất Cả"
            $(document).on('change', '#selectAll', function() {
                var isChecked = $(this).is(':checked');
                $('.stt-checkbox').prop('checked', isChecked).trigger('change');
            });

            function checkInputsAndToggleButtons() {
                var allCheckedBoxesHaveValue = true;
                $('.stt-checkbox:checked').each(function() {
                    var inputValue = $(this).closest('tr').find('.soluongnhapkho-input').val();
                    if (!inputValue || parseInt(inputValue, 10) === 0) {
                        allCheckedBoxesHaveValue = false;
                        return false; // Dừng vòng lặp nếu tìm thấy một trường hợp không thoả mãn
                    }
                });

                // Hiển thị hoặc ẩn các nút dựa trên kết quả kiểm tra
                if (allCheckedBoxesHaveValue) {
                    $('#inVatTu, #nhapKho').show();
                } else {
                    $('#inVatTu, #nhapKho').hide();
                }
            }
            $('#nhapKho').click(function(e) {
                e.preventDefault(); // Ngăn chặn hành động mặc định của form submit

                var selectedItems = [];
                    $('.stt-checkbox:checked').each(function() {
                        var row = $(this).closest('tr'); // Lấy hàng chứa checkbox đang được chọn
                        var soLuongNhapKho = row.find('td').eq(5).find('input').val(); // Lấy giá trị từ input ở cột thứ 6

                        // Lưu thông tin id và giá trị số lượng nhập kho vào một mảng
                        selectedItems.push({
                            id: $(this).val(),
                            soLuongNhapKho: soLuongNhapKho
                        });
                    });
                // Kiểm tra nếu không có mục nào được chọn
                if (selectedItems.length === 0) {
                    alert("Vui lòng chọn ít nhất một vật tư để nhập kho.");
                    return false;
                }

                // Tạo URL cho chuyển hướng
                var baseUrl = "{{ route('quetBarcodeNhapKho') }}"; // Đảm bảo đường dẫn này được in đúng trong mã HTML của bạn
                var query = $.param({ 'selectedItems': selectedItems });
                var redirectUrl = baseUrl + '?' + query;

                window.location.href = redirectUrl;
            });
        });

    </script>

  {{-- THỰC HIỆN IN BARCODE --}}
    <script>
        $(document).ready(function() {
            $('#inVatTu').click(function(e) {
                e.preventDefault(); // Ngăn chặn hành động mặc định của nút

                var iframe = $('#printFrame').get(0);
                var doc = iframe.contentDocument || iframe.contentWindow.document;

                // Khởi tạo nội dung HTML cần in
                doc.open();
                doc.write('<html><head><title>In Barcode</title>');
                doc.write('<style>');
                doc.write('body { font-family: Arial, sans-serif; font-size: 8pt; }'); // Giảm kích thước font xuống
                doc.write('.print-table { width: 100%; border-collapse: collapse; page-break-after: always; }');
                doc.write('td, th { border: 1px solid #ddd; text-align: left; padding: 8px; font-size: 8pt; }'); // Áp dụng font-size nhỏ hơn cho table
                doc.write('.ten-vat-tu { max-width: 200px; word-wrap: break-word; font-size: 8pt; }'); // Đặt kích thước font cụ thể cho tên vật tư
                doc.write('</style></head><body>');

                $('.stt-checkbox:checked').each(function() {
                    var $row = $(this).closest('tr');
                    var quantity = $row.find('.soluongnhapkho-input').val(); // Số lượng từ input
                    var donHang =  $('#orderTitle').text().replace('Đơn hàng: ', '');
                    var tenVatTu = $row.find('.tenvattu').text();
                    var barcode = $row.find('.barcode').html();

                    for (var i = 0; i < quantity; i++) {
                        // Bắt đầu một bảng mới cho mỗi hàng dữ liệu
                        doc.write('<table class="print-table"><tbody>');
                        doc.write(`<tr>
                                    <td>${donHang}<br>${tenVatTu}</td>
                                    <td>${barcode}</td>
                                </tr>`);
                        doc.write('</tbody></table>');
                    }
                });

                doc.write('</body></html>');
                doc.close();

                // Focus vào iframe và in nội dung
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            });


        });

    </script>
  {{-- TÌM KIẾM VẬT TƯ CHI TIẾT --}}
    <script>
        $(document).ready(function(){
            $("#timkiemVatTuChiTiet").click(function() {
                var tenvattu = $(".tenvattuchitiet").val() === "Tên vật tư" ? "" : $(".tenvattuchitiet").val();
                var maso = $(".masochitiet").val() === "Mã số" ? "" : $(".masochitiet").val();
                var donvitinh = $(".donvitinhchitiet").val() === "Đơn vị Tính" ? "" : $(".donvitinhchitiet").val()
                var orderId = $("#timkiemVatTuChiTiet").data('id');
                $.ajax({
                    url: "{{ route('timkiemvattuchitiet') }}", // Đường dẫn tới route xử lý tìm kiếm
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
                        // Xử lý dữ liệu trả về và hiển thị trong bảng
                        var tbody = $("#danhMucVatTuChiTiet tbody");
                        tbody.empty(); // Xóa nội dung hiện tại của tbody
                        if(data.supplies && data.supplies.length > 0){
                            $.each(data.supplies, function(index, supply) {
                                var barcodeHtml = supply.barcodeHtml;

                                tbody.append(
                                    `<tr id="supply-row-${supply.id}" data-id="${supply.id}">
                                        <td style="text-align:center;vertical-align: middle">${index + 1}</td>
                                        <td style="text-align:center;vertical-align: middle">${supply.tenvattu}</td>
                                        <td style="text-align:center;vertical-align: middle">${supply.maso}</td>
                                        <td style="text-align:center;vertical-align: middle">${supply.donvitinh}</td>
                                        <td style="text-align:center;vertical-align: middle">${supply.soluong}</td>
                                        <td style="text-align:center;vertical-align: middle">${supply.danhan}</td>
                                        <td style="text-align:center;vertical-align: middle">${supply.chuanhan}</td>
                                        <td style="text-align:center;vertical-align: middle">${supply.daxuat}</td>
                                        <td class="barcode">${barcodeHtml}<div>${supply.maso}</div></td>
                                    </tr>`
                                );
                            });
                        } else {
                            // Nếu không có supplies, chỉ hiển thị một hàng thông báo
                            tbody.append(
                                `<tr>
                                    <td colspan="10" style="text-align:center;vertical-align: middle">Không có dữ liệu</td>
                                </tr>`);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("Có lỗi xảy ra: " + error);
                    }
                });
            });
        });
    </script>

@endsection
