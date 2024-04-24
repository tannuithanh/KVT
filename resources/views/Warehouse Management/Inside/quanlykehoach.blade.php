@extends('Layout.app')
@section('style')
    <link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" />
    <style>
        .table-hover tbody tr:hover {
            cursor: pointer;
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
    </style>

    <style>

        .blink-warning {
            background-color: rgba(255, 255, 0, 0.329) !important
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

                  <!-- Default Tabs -->
                  <ul class="nav nav-tabs d-flex" id="myTabjustified" role="tablist">
                    <li class="nav-item flex-fill" role="presentation">
                      <button class="nav-link w-100 " id="home-tab" data-bs-toggle="tab" data-bs-target="#home-justified" type="button" role="tab" aria-controls="home" aria-selected="false" tabindex="-1">Danh sách đơn hàng</button>
                    </li>
                    <li class="nav-item flex-fill" role="presentation">
                      <button class="nav-link w-100 active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-justified" type="button" role="tab" aria-controls="profile" aria-selected="true">Danh sách vật tư</button>
                    </li>
                  </ul>
                  <div class="tab-content pt-2" id="myTabjustifiedContent">
                    <div class="tab-pane fade" id="home-justified" role="tabpanel" aria-labelledby="home-tab">
                        <div class="table-responsive mt-3">

                            <table class="table table-borderless table-bordered table-hover mt-2 vatTuDonHang">
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
                    {{-- DANH SÁCH VẬT TƯ --}}
                        <div class="tab-pane fade active show " id="profile-justified" role="tabpanel" aria-labelledby="profile-tab">
                            <div class="table-responsive">
                                    @if (session('errors'))
                                        <div class="alert alert-danger">
                                                <strong>Lỗi khi nhập dữ liệu:</strong>
                                                <ul>
                                                    @foreach (session('errors') as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                        </div>
                                    @endif
                                    @if (session('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif
                                <button class="btn btn-outline-primary" type="button" id="showExcelImportModal">+ Danh mục vật tư </button>
                                <table class="table table-borderless table-bordered table-hover mt-2 tabledanhmucvat">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center" scope="col">Stt</th>
                                            <th style="text-align: center" scope="col">Tên danh mục vật tư</th>
                                            <th style="text-align: center" scope="col">Nhà cung cấp</th>
                                            <th style="text-align: center" scope="col">Mô tả</th>
                                            <th style="text-align: center" scope="col">Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($project['catalogs'] as $index => $catalog)
                                            <tr data-catalog-id="{{$catalog->id}}" data-catalog-name="{{ $catalog['name'] }}">
                                                <td style="text-align: center;vertical-align: middle">{{ $index + 1 }}</td>
                                                <td style="text-align: center;vertical-align: middle">{{ $catalog['name'] }}</td>
                                                <td style="text-align: center;vertical-align: middle">{{ $catalog['nhacungcap'] }}</td>
                                                <td style="text-align: center;vertical-align: middle">{{ $catalog['description'] ?? 'Không có mô tả' }}</td>
                                                <td style="text-align: center;vertical-align: middle" class="action-column">
                                                    <button href="#" class="btn btn-danger xoadanhmucvattu" title="Xóa"><i class="ri ri-delete-bin-5-line"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                  </div><!-- End Default Tabs -->
                </div>
              </div>

        </div>
    </div>
</section>
{{-- DANH MỤC VẬT TƯ --}}
    {{-- MODAL HIỂN THỊ DANH MỤC VẬT TƯ--}}
        <div class="modal fade" id="vautucuadanhmuc" tabindex="-1">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Vật tư chi tiết</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="filter-box mt-3">
                            <div class="row">
                                <div class="col-sm-3">
                                    <select class="masovattu" aria-label="Default select example" style="width: 100%;" >
                                        <option selected="">Mã số</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <button type="submit" id="timkiemvattutrongdanhmuc" class="btn btn-primary" >Tìm kiếm</button>
                                </div>
                            </div>
                        </div>
                        <div class="table-responsive" style="max-height: 650px;">
                            <table class="table table-bordered vattucuadanhmuc">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; vertical-align: middle; position: sticky; top: 0;">STT</th>
                                        <th style="text-align: center; vertical-align: middle; position: sticky; top: 0;">Tên vật tư</th>
                                        <th style="text-align: center; vertical-align: middle; position: sticky; top: 0;">Mã số</th>
                                        <th style="text-align: center; vertical-align: middle; position: sticky; top: 0;">Đơn vị tính</th>
                                        <th style="text-align: center; vertical-align: middle; position: sticky; top: 0;">Số lượng</th>
                                        <th style="text-align: center; vertical-align: middle; position: sticky; top: 0;">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>

                        {{-- <button type="button" class="btn btn-outline-primary" id="themvattuchitiet" data-id="">+ Thêm vật tư</i></button> --}}

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">trở lại</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {{-- MODAL HIỂN TEXTAREA THAY ĐỔI --}}
        <div class="modal fade" id="editReasonModal" tabindex="-1" aria-hidden="true" style="background-color: rgb(0 0 0 / 59%)">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Nhập Lý Do Thay Đổi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <textarea class="form-control" id="changeReason" rows="4" placeholder="Hãy nhập lý do thay đổi tại đây..."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-primary" id="saveChanges">Lưu Thay Đổi</button>
                    </div>
                </div>
            </div>
        </div>
{{-- ĐƠN HÀNG --}}
    {{-- THÊM DANH MỤC VẬT TƯ --}}
        <div class="modal fade" id="themdanhmucvattubangfileexcel" tabindex="-1" role="dialog">
            <!-- Nội dung modal -->
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Thêm vật tư từ file Excel</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <a href="{{ asset('bieumau/sodonhang_nhacungcap_chiphi.xlsx') }}" download>
                                <button type="button" id="bieumau" class="btn btn-outline-primary">
                                    <i class="ri ri-download-2-fill"> Tải biểu mẫu</i>
                                </button>
                            </a>
                        </div>
                        <form action="{{ route('importSupplies') }}" method="POST" class="mt-2" enctype="multipart/form-data">
                            @csrf
                            <input type="number" class="form-control" value="{{$project->id}}" name="project_id" required hidden />
                            <div class="mb-3">
                                <input type="file" class="form-control" name="file" required />
                            </div>


                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-primary">Thêm</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    {{-- MODAL DANH MỤC VẬT TƯ CỦA ĐƠN HÀNG--}}
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
                        <div class="table-responsive" style="max-height: 650px;">
                            <table class="table table-bordered table-hover danhmucvattuchitiet">
                                <thead>
                                    <tr>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle;">STT</th>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle;">Tên vật tư</th>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle;">Mã số</th>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle;">Đơn vị tính</th>
                                        <th colspan="5" style="text-align: center;">Tình trạng</th>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle;">Mã Boardcode</th>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle;">ghi chú</th>
                                        <th rowspan="2" style="text-align: center; vertical-align: middle;">Thao tác</th>
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

                        {{-- <button type="button" class="btn btn-outline-primary" id="themvattuchitiet" data-id="">+ Thêm vật tư</i></button> --}}

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">trở lại</button>
                        </div>
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
@endsection

@section('script')
<script src="{{asset('assets/js/select2.min.js')}}"></script>
{{-- ĐƠN HÀNG --}}
  <script>
    $('.btn-secondary').click(function() {
        var supplyId = $(this).data('id'); // Lấy ID từ data-id của nút
        $('#supplyId').val(supplyId); // Đặt ID vào trường ẩn
    });
  </script>
  {{-- XÓA VẬT TƯ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Bắt sự kiện click cho nút xóa
            const deleteButtons = document.querySelectorAll('.delete-supplies');
            deleteButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    const ordersId = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Bạn có chắc chắn không?',
                        text: "Bạn sẽ không thể phục hồi sau khi xóa!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Có, xóa nó!',
                        cancelButtonText: 'Không, hủy bỏ!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{route('deleteDonHang')}}",
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}', // CSRF token (nếu sử dụng POST)
                                    ordersId: ordersId
                                },
                                success: function(response) {
                                    // Hiển thị SweetAlert khi xóa thành công
                                    Swal.fire(
                                        'Đã Xóa!',
                                        'Vật tư đã được xóa thành công.',
                                        'success'
                                    ).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.reload(); // Tải lại trang
                                        }
                                    });
                                },
                                error: function(error) {
                                    // Xử lý khi có lỗi
                                    Swal.fire(
                                        'Lỗi!',
                                        'Không thể xóa vật tư.',
                                        'error'
                                    );
                                }
                            });
                        }
                    })
                });
            });
        });
    </script>

  {{-- SỬA VẬT TƯ --}}
    <script>
          $(document).ready(function() {
              $('.edit-supperlies-model').on('click', function() {
                  var id = $(this).data('id');
                  var sodonhang = $(this).data('sodonhang');
                  var noidung = $(this).data('noidung');
                  var note = $(this).data('note');


                  var modal = $('#EditSupperlies');
                    modal.find('[name="OrderEditDonHang"]').val(id)
                  modal.find('[name="sodonhang-edit"]').val(sodonhang);
                  modal.find('[name="noidungphancum-edit"]').val(noidung);
                  modal.find('[name="maso-edit"]').val(maso);
                  modal.find('[name="note-edit"]').val(note);

              });
          });


    </script>
  {{-- HIỂN THỊ MODAL VẬT TƯ CHI TIẾT --}}
    <script>
            $(document).ready(function() {
                //HIỂN THỊ VẬT TƯ CHI TIẾT
                    $('.vatTuDonHang tbody tr').click(function() {
                        if ($(event.target).closest('.no-modal-trigger').length) {
                            // Nếu có, không làm gì cả để ngăn chặn hiển thị modal
                            return;
                        }
                        var orderId = $(this).data('id');
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
                                $('#timkiemVatTuChiTiet').attr('data-id', orderId);
                                var userFunctionId = {{$user->function_id}}
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

                                        var buttonsHtml = '';
                                            if (userFunctionId == 5) {
                                                buttonsHtml = `<td style="text-align:center;vertical-align: middle" class="action-btn">
                                                                    <button class="btn btn-sm btn-danger xoavattuchitiet" data-id="${item.id}">Xóa</button>
                                                                    <button class="btn btn-sm btn-primary chinhsuavattuchitiet" data-orderId="${orderId}" data-id="${item.id}" data-tenvattu="${item.tenvattu}" data-maso="${item.maso}" data-donvitinh="${item.donvitinh}" data-soluong="${item.soluong}">Sửa</button>
                                                                </td>`;
                                            } else {
                                                buttonsHtml = `<td style="text-align:center;vertical-align: middle" class="action-btn"></td>`;
                                            }


                                            var row = '<tr id="supply-row-' + item.id + '"  data-id="' + item.id + '">' +
                                            '<td style="text-align:center;vertical-align: middle">' + (index + 1) + '</td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' tenvattu">' + (item.tenvattu) + '</td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' maso">' + (item.maso) + '</td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' donvitinh">' + (item.donvitinh) + '</td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' soluong">' + (item.soluong) + '</td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' soluongnhapkho">' + totalNhapKho + '</td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' soluongdatchatluong">' + totalDatChatLuong + '</td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' chuanhan">' + (item.chuanhan) + '</td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' daxuat">' + (item.daxuat) + '</td>' +
                                            '<td class="barcode ' + rowClass + '">' + (item.barcodeHtml || '') + '<div>' + (item.maso || '') + '</div></td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="' + rowClass + ' ghichu">' + (item.ghichu !== null ? item.ghichu : '') + '</td>' +
                                            buttonsHtml +
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
                //SỬA VẬT TƯ CHI TIẾT
                    $('#danhMucVatTuChiTiet tbody').on('click', '.chinhsuavattuchitiet', function() {
                        var orderId = $(this).data('orderid');
                        var id = $(this).data('id');
                        var tenvattu = $(this).data('tenvattu');
                        var maso = $(this).data('maso');
                        var donvitinh = $(this).data('donvitinh');
                        var soluong = $(this).data('soluong');
                        $('input[name="tenvattu-edit"]').val(tenvattu);
                        $('input[name="maso-edit"]').val(maso);
                        $('select[name="donvitinh-edit"]').val(donvitinh);
                        $('input[name="soluongvattuthemvao-edit"]').val(soluong);
                        $('#suavattu-edit').data('id', id);
                        $('#suavattuthucong').modal('show');
                    });
                    $('#suavattu-edit').click(function() {
                        var tenvattu = $('input[name="tenvattu-edit"]').val();
                        var maso = $('input[name="maso-edit"]').val();
                        var donvitinh = $('select[name="donvitinh-edit"]').val();
                        var soluong = $('input[name="soluongvattuthemvao-edit"]').val();
                        var ghichu = $('textarea[name="ghichuvattuchitiet-edit"]').val();
                        var id = $(this).data('id'); // Lấy ID của vật tư từ nút "Sửa vật tư"
                            $.ajax({
                                url: "{{ route('suavattuchitiet') }}",
                                type: 'POST',
                                data: {
                                    id: id, // Gửi ID của vật tư cùng với dữ liệu khác
                                    tenvattu: tenvattu,
                                    maso: maso,
                                    donvitinh: donvitinh,
                                    soluong: soluong,
                                    ghichu: ghichu,
                                    _token: $('meta[name="csrf-token"]').attr('content')
                                },
                                success: function(response) {
                                    var rowId = "#supply-row-" + id;
                                    $(rowId).find(".tenvattu").text(tenvattu);
                                    $(rowId).find(".maso").text(maso);
                                    $(rowId).find(".donvitinh").text(donvitinh);
                                    $(rowId).find(".soluong").text(soluong);
                                    $(rowId).find(".danhan").text(response.danhan);
                                    $(rowId).find(".chuanhan").text(response.chuanhan);
                                    $(rowId).find(".daxuat").text(response.daxuat);
                                    $(rowId).find(".ghichu").text(ghichu);
                                    var updatedColumnContent = response.barcodeHtml + '<div>' + maso + '</div>';
                                    $(rowId).find('td.barcode').html(updatedColumnContent);
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Thành công!',
                                        text: 'Cập nhật thành công.',
                                    });
                                    $('#suavattuthucong').modal('hide');
                                },
                                error: function(error) {
                                    // Sử dụng SweetAlert2 để hiển thị thông báo lỗi
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Lỗi!',
                                        text: 'Có lỗi xảy ra khi cập nhật.',
                                    });
                                }
                            });
                        });
                    });
    </script>
  {{-- XÓA VẬT TƯ CHI TIẾT --}}
        <script>
            $(document).on('click', '.xoavattuchitiet', function() {
                var button = $(this); // Lưu trữ tham chiếu đến button được nhấn
                var supplyId = button.data('id'); // Lấy ID của vật tư từ attribute data-id của button

                Swal.fire({
                    title: 'Bạn có muốn xóa vật tư này không?',
                    text: "Bạn không thể hoàn tác hành động này!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Có, xóa nó!',
                    cancelButtonText: 'Không, hủy bỏ!',
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Thực hiện yêu cầu AJAX để xóa vật tư
                        $.ajax({
                            url: "{{ route('xoavattuchitiet') }}", // Đường dẫn tới route xử lý trong Laravel
                            type: 'POST',
                            data: {
                                id: supplyId, // Gửi ID của vật tư cần xóa
                                _token: $('meta[name="csrf-token"]').attr('content') // CSRF token
                            },
                            success: function(response) {
                                // Xử lý khi xóa thành công
                                Swal.fire(
                                    'Đã Xóa!',
                                    'Vật tư của bạn đã được xóa.',
                                    'success'
                                );
                                button.closest('tr').remove();
                            },
                            error: function(xhr) {
                                // Xử lý khi có lỗi xảy ra
                                Swal.fire(
                                    'Lỗi!',
                                    'Không thể xóa vật tư. Vui lòng thử lại.',
                                    'error'
                                );
                            }
                        });
                    }
                })
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
                var ghichu = $('textarea[name="ghichuvattuchitiet"]').val();
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
                        ghichu:ghichu,
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
                                    '<td style="text-align:center;vertical-align: middle" class="ghichu">' + (item.ghichu !== null ? item.ghichu : '') + '</td>' +
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
            if ($(event.target).closest('.action-btn').length) {
                // Bỏ qua nếu click vào nút thuộc class "action-btn"
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
                            console.log(transaction)
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
  {{-- TÌM KIẾM VẬT TƯ CHI TIẾT --}}
    <script>
        $(document).ready(function(){
            $("#timkiemVatTuChiTiet").click(function() {
                var tenvattu = $(".tenvattuchitiet").val() === "Tên vật tư" ? "" : $(".tenvattuchitiet").val();
                var maso = $(".masochitiet").val() === "Mã số" ? "" : $(".masochitiet").val();
                var donvitinh = $(".donvitinhchitiet").val() === "Đơn vị Tính" ? "" : $(".donvitinhchitiet").val()
                var orderId = $("#timkiemVatTuChiTiet").data('id');
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
{{-- DANH MỤC VẬT TƯ --}}
    {{-- HIỂN THỊ MODAL VẬT TƯ CỦA DANH MỤC --}}
        <script>
            $(document).ready(function() {
                $('.tabledanhmucvat tbody').on('click', 'tr', function(event) {
                    if ($(event.target).closest('.action-column').length) {
                        // Bỏ qua nếu click vào nút thuộc class "action-btn"
                        return;
                    }
                    var catalogId = $(this).data('catalog-id');
                    var catalogName = $(this).data('catalog-name');
                    $.ajax({
                        url: '{{ route("vattutrongdanhmuc") }}',
                        type: 'POST',
                        data: {
                            catalog_id: catalogId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('.masovattu').empty().append('<option selected="">Mã số</option>');

                            // Thêm các option mới từ dữ liệu server
                            $.each(response.supplies, function(index, supply) {
                                $('.masovattu').append(`<option value="${supply.id}">${supply.maso}</option>`);
                            });

                            // Khởi tạo Select2 cho dropdown
                            $('.masovattu').select2({
                                placeholder: "Chọn mã số",
                                allowClear: true
                            });
                            $('.vattucuadanhmuc tbody').empty();
                            $.each(response.supplies, function(index, supply) {
                                var noteIcon = supply.note ? `<span class="bi bi-info-circle-fill text-info" style="cursor:pointer;" data-bs-toggle="popover" title="Nguyên nhân" data-bs-content="${supply.note}"></span>` : '';
                                $('.vattucuadanhmuc tbody').append(
                                    `<tr data-supply-id="${supply.id}">
                                        <td style="text-align: center;vertical-align: middle">${index + 1}</td>
                                        <td style="text-align: center;vertical-align: middle">${supply.tenvattu} ${noteIcon}</td>
                                        <td style="text-align: center;vertical-align: middle">${supply.maso}</td>
                                        <td style="text-align: center;vertical-align: middle">${supply.donvitinh}</td>
                                        <td style="text-align: center;vertical-align: middle">${supply.soluong}</td>
                                        <td class='action-column' style="text-align: center; vertical-align: middle;">
                                            <button class="btn btn-secondary thaydoivattu" title="Thay đổi">
                                                <i class="bx bx-transfer"></i>
                                            </button>
                                        </td>
                                    </tr>`
                                );
                            });
                            $('[data-bs-toggle="popover"]').popover();  // Kích hoạt tất cả các popover
                            $('#tongsovattu').text('Tổng vật tư: ' + response.totalSupplies);
                            $('#vautucuadanhmuc .modal-title').html(`Danh mục vật tư: ${catalogName}`);
                            $('#vautucuadanhmuc').modal('show');
                        }
                    });
                });
            });
        </script>
    {{-- XÓA VẬT TƯ --}}
        <script>
            $(document).ready(function() {
                $('.xoadanhmucvattu').click(function(event) {
                    event.stopPropagation();  // Ngăn không cho sự kiện lan truyền lên hàng
                    var row = $(this).closest('tr');
                    var catalogId = row.data('catalog-id');
                    var catalogName = row.data('catalog-name');

                    Swal.fire({
                        title: 'Bạn có chắc chắn?',
                        text: `Bạn có muốn xóa danh mục ${catalogName} không? Tất cả vật tư và các đơn hàng liên quan sẽ bị xóa.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Có, xóa nó!',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Thực hiện gọi AJAX để xóa
                            $.ajax({
                                url: '{{ route("xoa_Danhmuc") }}',  // Sử dụng helper route của Laravel để đảm bảo URL chính xác
                                method: 'POST',  // Phương thức POST như đã định nghĩa trong route
                                data: {
                                    catalog_id: catalogId,  // Gửi ID của danh mục cần xóa
                                    _token: '{{ csrf_token() }}'  // CSRF token cho Laravel
                                },
                                success: function(response) {
                                    Swal.fire(
                                        'Đã Xóa!',
                                        'Danh mục và các dữ liệu liên quan đã được xóa.',
                                        'success'
                                    );
                                    // Xóa hàng từ bảng hoặc làm mới trang
                                    row.remove(); // Xóa hàng nếu không muốn tải lại trang
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire(
                                        'Lỗi!',
                                        'Không thể xóa danh mục: ' + xhr.responseText,
                                        'error'
                                    );
                                }
                            });
                        }
                    });
                });
            });
        </script>
    {{-- CHUYỂN ĐỔI THÀNH INPUT VÀ MODAL NGUYÊN NHÂN --}}
        <script>
                $(document).ready(function() {
                    // Xử lý khi nhấp vào nút "Thay đổi"
                    $('.vattucuadanhmuc tbody').on('click', '.btn-secondary', function(event) {
                        event.preventDefault();
                        var $row = $(this).closest('tr');
                        var supplyId = $row.data('supply-id');
                        var originalData = [];

                        // Chuyển đổi các giá trị td thành input
                        $row.children('td').not(':first, :last').each(function(index) {
                            var content = $(this).text();
                            originalData.push(content);
                            var fieldName = '';
                            switch (index) {
                                case 0: fieldName = 'tenvattu'; break;
                                case 1: fieldName = 'maso'; break;
                                case 2: fieldName = 'donvitinh'; break;
                                case 3: fieldName = 'soluong'; break;
                            }
                            var inputHtml = `<input type="text" class="form-control" name="${fieldName}" value="${content}">`;
                            $(this).html(inputHtml);
                        });

                        // Thay đổi nút thành "Lưu"
                        $(this).html('<i class="ri-save-3-line"></i>').attr('title', 'Lưu').removeClass('btn-secondary').addClass('btn-primary save-btn');
                        $row.data('original', originalData);
                    });

                    // Xử lý khi nhấp vào nút "Lưu"
                    $('.vattucuadanhmuc tbody').on('click', '.save-btn', function(event) {
                        var $row = $(this).closest('tr');
                        var newData = $row.find('input').map(function() { return $(this).val(); }).get();
                        var originalData = $row.data('original');
                        var hasChanged = newData.some((value, index) => value !== originalData[index]);

                        // Kiểm tra có thay đổi không và hiển thị modal nhập lý do
                        if (hasChanged) {
                            $('#editReasonModal').modal('show');
                        } else {
                            // Nếu không có thay đổi, khôi phục lại trạng thái ban đầu
                            $row.children('td').not(':first, :last').each(function(index) {
                                $(this).html(originalData[index]);
                            });
                            $(this).html('<i class="bx bx-transfer"></i>').attr('title', 'Thay đổi').removeClass('btn-primary save-btn').addClass('btn-secondary');
                        }
                    });

                    // Xử lý khi nhấn nút 'Lưu Thay Đổi' trên modal
                    $('#saveChanges').on('click', function() {
                        var reason = $('#changeReason').val();
                        if (!reason.trim()) {
                            alert('Vui lòng nhập lý do thay đổi.');
                            return;
                        }
                        var changes = [];
                        $('.vattucuadanhmuc tbody tr').each(function() {
                            if ($(this).find('input').length > 0) {
                                var row = {
                                    supply_id: $(this).data('supply-id'),
                                    tenvattu: $(this).find('input[name="tenvattu"]').val(),
                                    maso: $(this).find('input[name="maso"]').val(),
                                    donvitinh: $(this).find('input[name="donvitinh"]').val(),
                                    soluong: $(this).find('input[name="soluong"]').val()
                                };
                                changes.push(row);
                            }
                        });

                        $.ajax({
                            type: 'POST',
                            url: '{{ route("thayTheVatTu") }}',
                            data: {
                                note: reason,
                                supplies: changes
                            },
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                // Cập nhật lại dữ liệu trên giao diện người dùng
                                $('.vattucuadanhmuc tbody tr').each(function() {
                                    if ($(this).find('input').length > 0) {
                                        $(this).children('td').not(':first, :last').each(function(index) {
                                            var input = $(this).find('input');
                                            var text = input.val(); // Lấy giá trị từ input
                                            var htmlContent = text; // Nội dung HTML mặc định là giá trị text
                                            if (index === 0) { // Giả sử chỉ thêm icon cho cột 'Tên vật tư'
                                                htmlContent += ` <span class="bi bi-info-circle-fill text-info" style="cursor:pointer;" data-bs-toggle="popover" title="Nguyên nhân" data-bs-content="${reason}"></span>`;
                                            }
                                            $(this).html(htmlContent); // Cập nhật HTML của td
                                        });
                                        // Đặt lại nút sau khi lưu
                                        $(this).find('.save-btn').html('<i class="bx bx-transfer"></i>').attr('title', 'Thay đổi').removeClass('btn-primary save-btn').addClass('btn-secondary');
                                    }
                                });
                                $('#editReasonModal').modal('hide');
                                // Kích hoạt popover mới thêm vào
                                $('[data-bs-toggle="popover"]').popover();
                            },
                            error: function(error) {
                                console.log(error);
                                alert('Có lỗi xảy ra, vui lòng thử lại.');
                            }
                        });
                    });
                });
        </script>
    {{-- TÌM KIẾM VẬT TƯ TRONG DANH MỤC --}}
        <script>
            $(document).ready(function() {
                $('#timkiemvattutrongdanhmuc').click(function() {
                    var selectedId = $('.masovattu').val(); // Lấy ID từ dropdown

                    if (!selectedId) {
                        alert('Vui lòng chọn mã số vật tư để tìm kiếm.');
                        return;
                    }

                    $.ajax({
                        type: 'POST',
                        url: '{{ route("timKiemVatTu") }}',
                        data: {
                            id: selectedId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            console.log(response.supply); // Log để debug
                            if (response.status === 'success') {
                                // Xóa bảng hiện tại và thêm các hàng mới từ kết quả tìm kiếm
                                $('.vattucuadanhmuc tbody').empty();
                                    var noteIcon = response.supply.note ? `<span class="bi bi-info-circle-fill text-info" style="cursor:pointer;" data-bs-toggle="popover" title="Nguyên nhân" data-bs-content="${response.supply.note}"></span>` : '';
                                    $('.vattucuadanhmuc tbody').append(
                                        `<tr data-supply-id="${response.supply.id}">
                                            <td style="text-align: center;vertical-align: middle">1</td>
                                            <td style="text-align: center;vertical-align: middle">${response.supply.tenvattu} ${noteIcon}</td>
                                            <td style="text-align: center;vertical-align: middle">${response.supply.maso}</td>
                                            <td style="text-align: center;vertical-align: middle">${response.supply.donvitinh}</td>
                                            <td style="text-align: center;vertical-align: middle">${response.supply.soluong}</td>
                                            <td class='action-column' style="text-align: center; vertical-align: middle;">
                                                <button class="btn btn-secondary thaydoivattu" title="Thay đổi">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>
                                            </td>
                                        </tr>`
                                    );

                                $('[data-bs-toggle="popover"]').popover(); // Kích hoạt tất cả các popover

                            } else {
                                alert('Không tìm thấy vật tư với mã số này.');
                            }
                        },
                        error: function(error) {
                            console.error('Lỗi khi tìm kiếm:', error);
                            alert('Có lỗi xảy ra, vui lòng thử lại.');
                        }
                    });
                });
            });
        </script>
    {{-- HIỂN THỊ MODAL THÊM VẬT TƯ BẰNG EXCEL --}}
        <script>
            $(document).ready(function() {
                $('#showExcelImportModal').click(function() {
                    // Mở modal mới
                    $('#themdanhmucvattubangfileexcel').modal('show');

                    // Tăng z-index cho modal mới để nó hiển thị phía trên
                    // Bạn có thể cần điều chỉnh giá trị z-index tuỳ thuộc vào cấu trúc CSS của bạn
                    $('.modal-backdrop').last().css('z-index', parseInt($('.modal-backdrop').first().css('z-index')) + 10);
                    $('#themdanhmucvattubangfileexcel').css('z-index', parseInt($('#themdanhmucvattubangfileexcel').css('z-index')) + 10);
                });

                // Đảm bảo rằng modal đóng đúng cách và không làm ảnh hưởng tới modal khác
                $('#themdanhmucvattubangfileexcel').on('hidden.bs.modal', function () {
                    $('body').addClass('modal-open'); // giữ trạng thái scroll cho modal còn lại
                });
            });
        </script>


@endsection
