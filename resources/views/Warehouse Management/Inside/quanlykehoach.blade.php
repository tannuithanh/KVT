@extends('Layout.app')
@section('style')
  <style>

    .table-hover tbody tr:hover {
        cursor: pointer;
    }
  </style>
@endsection
@section('title')
    Danh mục vật tư
@endsection

@section('content')
<div class="pagetitle">
    <h1>Đơn hàng</h1>
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
                      <span style="font-size: 18px;font-weight: 600;color: #012970;">Tổng số vật tư: <span  style="color: black">{{ $totalSuppliesForProject ?? 0 }}</span>                                          </h5>
                      <button type="button" class="btn btn-outline-primary ri-search-line" data-bs-toggle="modal" data-bs-target="#Timkiemvattu"> Tìm kiếm</button>

                        <button class="btn btn-outline-primary" type="button" data-bs-target="#themdanhmucvattubangfileexcel" id="dropdownMenuButton" data-bs-toggle="modal" data-bs-target="#themdanhmucvattubangfileexcel">
                            Thêm đơn hàng và vật tư
                        </button>
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

                                        <tr>
                                        <th style="text-align: center" rowspan="2" scope="col">Stt</th>
                                        <th style="text-align: center" rowspan="2" scope="col">Số đơn hàng</th>
                                        <th style="text-align: center" rowspan="2" scope="col">NCC</th>
                                        <th style="text-align: center" rowspan="2" scope="col">Nội dung</th>
                                        <th style="text-align: center" colspan="4" scope="col">Tình trạng</th>
                                        <th style="text-align: center" rowspan="2" scope="col">Chi Phí</th>
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
                                    @forelse ($orders as $index => $order)
                                        <tr>
                                            <td class="no-modal-trigger" style="text-align: center;vertical-align: middle;">{{ $stt++ }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->sodonhang }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->nhacungcap }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->noidung }}</td>
                                            <!-- Cột tổng số lượng vật tư -->
                                            <td style="text-align: center;vertical-align: middle;">{{ $totals[$order->id] ?? '0' }}</td>
                                            <!-- Các cột khác (Đã nhận, Chưa nhận, Đã xuất) -->
                                            <td style="text-align: center;vertical-align: middle;"></td>
                                            <td style="text-align: center;vertical-align: middle;"></td>
                                            <td style="text-align: center;vertical-align: middle;"></td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->chiphi }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->ghichu }}</td>
                                            <td class="no-modal-trigger" style="text-align: center;vertical-align: middle;">
                                              <button class="btn btn-sm btn-primary edit-supperlies-model"
                                                  data-bs-toggle="modal" data-note="{{ $order->ghichu }}"
                                                  data-noidung="{{$order->noidung}}"
                                                  data-sodonhang="{{$order->sodonhang}}"
                                                  data-id="{{$order->id}}"
                                                  data-bs-target="#EditSupperlies">Sửa</button>
                                              <button class="btn btn-sm btn-danger delete-supplies" data-id="{{$order->id}}">Xóa</button>
                                              {{-- <button class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#themsoluongvattu" data-id="{{$order->id}}">Thêm</button> --}}
                                            </td>
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
              <div class="modal-body">
                <form action="" method="GET">
                  <input type="text" value="" name="project_id" hidden>
                <div class="col-sm-12">
                  <div class="form-check d-flex align-items-center mt-2">
                    <!-- Checkbox -->
                    <div class="form-check col-sm-3">
                      <input class="form-check-input" type="checkbox" id="tenvattu" >
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
                      <input class="form-check-input" type="checkbox" id="ngaynhan" >
                      <label class="form-check-label" for="ngaynhan">
                        Ngày nhập kho:
                      </label>
                    </div>
                    <input type="date" name="ngaynhan" id="ngaynhanSuppelies" class="form-control" disabled>
                  </div>
                  <div class="form-check d-flex align-items-center mt-2">
                    <!-- Checkbox -->
                    <div class="form-check col-sm-3">
                      <input class="form-check-input" type="checkbox" id="nhacungcap" >
                      <label class="form-check-label" for="nhacungcap">
                        Nhà cung cấp
                      </label>
                    </div>

                    <select class="form-select ml-auto" id="nhacungcapSuppelies" name="nhacungcapSuppeliesSelect" aria-label="Default select example" required disabled>
                      <option selected="">Chọn hạng mục</option>
                      <option selected="">Nhà cung cấp 1</option>
                      <option selected="">Nhà cung cấp 2</option>
                    </select>
                  </div>

                  <div class="form-check d-flex align-items-center mt-2">
                    <!-- Checkbox -->
                    <div class="form-check col-sm-3">
                      <input class="form-check-input" type="checkbox" id="maso" >
                      <label class="form-check-label" for="maso">
                        Mã số:
                      </label>
                    </div>

                    <input type="number" class="form-control" id="masoSuppelies" disabled>
                  </div>

                  <div class="form-check d-flex align-items-center mt-2">
                    <!-- Checkbox -->
                    <div class="form-check col-sm-3">
                      <input class="form-check-input" type="checkbox" id="status" >
                      <label class="form-check-label" for="status">
                        Trạng thái:
                      </label>
                    </div>

                    <!-- Select menu -->
                    <select class="form-select ml-auto" id="statusSuppeliesSelect" name="status" aria-label="Default select example" disabled>
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
                          <button type="button" id="bieumau" class="btn btn-success">
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
{{-- VẬT TƯ CHI TIẾT --}}
  {{-- <div class="modal fade" id="lichsuvattu" tabindex="-1" style="display: none;" aria-modal="false" role="dialog">
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
  </div> --}}

{{-- THÊM VẬT TƯ THỦ CÔNG --}}
  {{-- <div class="modal fade" id="themvattuthucong" tabindex="-1" style="display: none;" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Thêm thủ công</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body ">
            <form action="{{ route('importThuCong') }}" class="row g-3" method="POST">
              @csrf
              <input value="{{$project->id}}" name="project_id" hidden>
                <div class="col-md-12">
                  <label for="inputName5" class="form-label">Số đơn hàng</label>
                  <input type="text" class="form-control" name="sodonhang">
                </div>
                <div class="col-md-6">
                  <label for="inputName5" class="form-label">Tên vật tư</label>
                  <input type="text" class="form-control"name="tenvattu">
                </div>
                <div class="col-md-6">
                  <label for="inputState" class="form-label">Nhà cung cấp</label>
                  <select id="inputState" class="form-select" name="nhacungcap">
                    @foreach($providers as $provider)
                        <optgroup label="{{ $provider->name }}">
                            @foreach($provider->details as $detail)
                                <option value="{{ $detail->name }}">{{ $detail->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                </div>
                <div class="col-md-12">
                  <label for="inputName5" class="form-label">Nội dung</label>
                  <textarea class="form-control" name="noidungphancum"></textarea>
                </div>
                <div class="col-md-12">
                  <label for="inputName5" class="form-label">Mã số</label>
                  <input type="text" style="text-transform: uppercase;" class="form-control" name="maso">
                </div>
                <div class="col-md-4">
                  <label for="inputEmail5" class="form-label">Đơn vị tính</label>
                  <select id="inputState" class="form-select" name="donvitinh">
                    <option>Cái</option>
                    <option>Bộ</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="inputEmail5" class="form-label">Số lượng</label>
                  <input type="number" name="soluong" class="form-control" id="inputEmail5">
                </div>
                <div class="col-md-4">
                  <label for="inputEmail5" class="form-label">Chi phí</label>
                  <select id="inputState" class="form-select"  name="chiphi">
                    <option value="BUS">BUS</option>
                    <option value="TẢI">TẢI</option>
                    <option value="ROYAL">ROYAL</option>
                    <option value="MAZDA">MAZDA</option>
                  </select>
                </div>
                <div class="col-md-12">
                  <label for="inputName5" class="form-label">Ghi chú</label>
                  <textarea class="form-control" name="note"></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button class="btn btn-primary" type="submit">Thêm</button>
              </div>
            </form>
        </div>
      </div>
    </div> --}}
{{-- THÊM SỐ LƯỢNG VẬT TƯ --}}
  <div class="modal fade" id="themsoluongvattu" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Thêm số lượng vật tư</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form method="post" action="{{ route('addQuantity') }}">
          @csrf
          <div class="modal-body">
            <div class="row mb-3">
              <label for="inputText" class="col-sm-4 col-form-label">Số lượng</label>
              <div class="col-sm-8">
                <input type="hidden" id="supplyId" name="supplyId" value="">
                <input type="number" name="soluong" class="form-control">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="submit" class="btn btn-primary">Thêm</button>
          </div>
        </form>
      </div>
    </div>
  </div>
{{-- SỬA VẬT TƯ THỦ CÔNG --}}
  <div class="modal fade" id="EditSupperlies" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Sửa vật tư</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body ">
          <form action="{{route('suavattu')}}" class="row g-3" method="POST">
            @csrf
            <input id="OrderEdit" name="OrderEdit" value="" hidden>
              <div class="col-md-12">
                <label for="inputName5" class="form-label">Số đơn hàng</label>
                <input type="text" class="form-control" name="sodonhang-edit">
              </div>
              <div class="col-md-12">
                <label for="inputState" class="form-label">Nhà cung cấp</label>
                <select id="inputState" class="form-select" name="nhacungcap-edit">
                    @foreach($providers as $provider)
                        <optgroup label="{{ $provider->name }}">
                            @foreach($provider->details as $detail)
                                <option value="{{ $detail->name }}">{{ $detail->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
              </div>
              <div class="col-md-12">
                <label for="inputName5" class="form-label">Nội dung</label>
                <textarea class="form-control" name="noidungphancum-edit"></textarea>
              </div>
              <div class="col-md-12">
                <label for="inputEmail5" class="form-label">Chi phí</label>
                <select id="inputState" class="form-select"  name="chiphi-edit">
                  <option value="BUS">BUS</option>
                  <option value="TẢI">TẢI</option>
                  <option value="ROYAL">ROYAL</option>
                  <option value="MAZDA">MAZDA</option>
                </select>
              </div>
              <div class="col-md-12">
                <label for="inputName5" class="form-label">Ghi chú</label>
                <textarea class="form-control" name="note-edit"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
              <button class="btn btn-primary" type="submit">Sửa</button>
            </div>
          </form>
      </div>
    </div>
  </div>
{{-- MODAL DANH MỤC VẬT TƯ--}}
<div class="modal fade" id="fullscreenModal" tabindex="-1">
    <div class="modal-dialog modal-fullscreen">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Vật tư chi tiết</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Đơn hàng: BHCKI1221
          <table class="table table-bordered">
            <thead>
              <tr>
                <th rowspan="2" style="text-align: center; vertical-align: middle;">STT</th>
                <th rowspan="2" style="text-align: center; vertical-align: middle;">Tên vật tư</th>
                <th rowspan="2" style="text-align: center; vertical-align: middle;">Mã số</th>
                <th rowspan="2" style="text-align: center; vertical-align: middle;">Đơn vị tính</th>
                <th colspan="4" style="text-align: center;">Tình trạng</th>
                <th rowspan="2" style="text-align: center; vertical-align: middle;">Mã Boardcode</th>
              </tr>
              <tr>
                <th style="text-align: center; vertical-align: middle;">Tổng</th>
                <th style="text-align: center; vertical-align: middle;">Đã nhận</th>
                <th style="text-align: center; vertical-align: middle;">Chưa nhận</th>
                <th style="text-align: center; vertical-align: middle;">Đã xuất</th>
              </tr>
            </thead>
            <tbody>

            </tbody>
          </table>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">trở lại</button>
          <button type="button" class="btn btn-primary">Lưu</button>
        </div>
      </div>
    </div>
</div>
@endsection

@section('script')
  <script>

  </script>

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
                modal.find('[name="sodonhang-edit"]').val(sodonhang);
                modal.find('[name="noidungphancum-edit"]').val(noidung);
                modal.find('[name="maso-edit"]').val(maso);
                modal.find('[name="note-edit"]').val(note);
                $('#OrderEdit').val(id);
            });
        });


  </script>
@endsection
