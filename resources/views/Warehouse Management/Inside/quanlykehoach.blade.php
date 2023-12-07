@extends('Layout.app')

@section('title')
    Danh mục vật tư
@endsection

@section('content')
<div class="pagetitle">
    <h1>Danh mục vật tư</h1>
    <nav>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Trang chủ</a></li>
        <li class="breadcrumb-item">Quản lý kế hoạch</li>
        <li class="breadcrumb-item"><a href="{{route('listBrand')}}">Thương hiệu</a></li>
        <li class="breadcrumb-item"><a href="{{ route('listProject', $brandId) }}">Dự án</a></li>
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
                        <span style="font-size: 18px;font-weight: 600;color: #012970;">Thương hiệu: <span style="color: red">{{$project->brand->name}}</span> |
                        <span style="font-size: 18px;font-weight: 600;color: #012970;">Dự án: <span  style="color: red">{{$project->name}}</span> |
                        <span style="font-size: 18px;font-weight: 600;color: #012970;">Tổng số vật tư: <span  style="color: red">{{ $totalSupplies ?? 0 }}</span>
                    </h5>
                    <button type="button" class="btn btn-outline-success ri-search-line" data-bs-toggle="modal" data-bs-target="#Timkiemvattu"> Tìm kiếm</button>

                        <button class="btn btn-outline-success dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Thêm vật tư
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#themdanhmucvattubangfileexcel">Thêm bằng cách import file</a></li>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#modalThemThuCong">Thêm thủ công</a></li>
                        </ul>


                    <table class="table table-borderless table-bordered table-hover mt-2">
                        <thead>
                            <tr>
                                <th style="text-align: center" scope="col">Số đơn hàng</th>
                                <th style="text-align: center" scope="col">NCC</th>
                                <th style="text-align: center" scope="col">Chi Phí</th>
                                <th style="text-align: center" scope="col">Nội dung/phân cụm</th>
                                <th style="text-align: center" scope="col">STT</th>
                                <th style="text-align: center" scope="col">Tên vật tư</th>
                                <th style="text-align: center" scope="col">Mã số</th>
                                <th style="text-align: center" scope="col">Đơn vị tính</th>
                                <th style="text-align: center" scope="col">Số lượng</th>
                                <th style="text-align: center" scope="col">Ngày nhận</th>
                                <th style="text-align: center" scope="col">Mã barcode</th>
                                <th style="text-align: center" scope="col">Ghi chú</th>
                                <th style="text-align: center" scope="col">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

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
                    <a href="{{ asset('bieumau/Sodonhan_NCC_Chiphi.xlsx') }}" download>
                        <button type="button" id="bieumau" class="btn btn-success">
                            <i class="ri ri-download-2-fill"> Tải biểu mẫu</i>
                        </button>
                    </a>
                </div>
                <form action="{{ route('importSupplies') }}" method="POST" class="mt-2" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <input type="file" class="form-control" name="file" required />
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
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.saveSupplies').on('click', function() {
            var formData = new FormData();
            formData.append('file-upload', $('#file-upload')[0].files[0]);

            $.ajax({
                url: '{{ route("importSupplies") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    console.log('File uploaded successfully');
                    // Thêm mã xử lý khi thành công
                },
                error: function(response) {
                    console.log('Error uploading file');
                    // Thêm mã xử lý khi có lỗi
                }
            });
        });
    });

</script>
<script>
  $(document).ready(function(){
      $(".form-check-input").click(function(){
          var checkboxId = $(this).attr('id');
          var correspondingInput = $("#" + checkboxId + "Suppelies");
          var correspondingSelect = $("#" + checkboxId + "SuppeliesSelect");

          if ($(this).is(':checked')){
              correspondingInput.removeAttr('disabled');
              correspondingSelect.removeAttr('disabled');
          } else {
              correspondingInput.attr('disabled', 'disabled');
              correspondingSelect.attr('disabled', 'disabled');
          }
      });
  });
  </script>
@endsection
