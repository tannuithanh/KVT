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
                        <!-- Nội dung thông báo thành công -->
                          @if(session('success'))
                              <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show mt-3" role="alert">
                                  {{ session('success') }}
                                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                              </div>
                          @endif
                    
                    <table class="table table-borderless table-bordered table-hover mt-2">
                      
                        <thead>
                            <tr>
                                
                                <tr>
                                <th style="text-align: center" rowspan="2" scope="col">STT</th>
                                <th style="text-align: center" rowspan="2" scope="col">Số đơn hàng</th>
                                <th style="text-align: center" rowspan="2" scope="col">Tên vật tư</th>
                                <th style="text-align: center" rowspan="2" scope="col">NCC</th>
                                <th style="text-align: center" rowspan="2" scope="col">Nội dung/phân cụm</th>
                                <th style="text-align: center" rowspan="2" scope="col">Mã số</th>
                                <th style="text-align: center" rowspan="2" scope="col">Đơn vị tính</th>
                                <th style="text-align: center" colspan="3" scope="col">Số lượng</th>
                                <th style="text-align: center" rowspan="2" scope="col">Ngày nhận</th>
                                <th style="text-align: center" rowspan="2" scope="col">Chi Phí</th>
                                <th style="text-align: center" rowspan="2" scope="col">Barcode</th>
                                <th style="text-align: center" rowspan="2" scope="col">Trạng thái</th>
                                <th style="text-align: center" rowspan="2" scope="col">Ghi chú</th>
                                <th style="text-align: center" rowspan="2" scope="col">Thao tác</th>
                              </tr>
                              <tr>
                                  <!-- Các cột khác ở đây -->
                                  <th style="text-align: center" scope="col">Tổng</th>
                                  <th style="text-align: center" scope="col">Đã nhận</th>
                                  <th style="text-align: center" scope="col">Chưa nhận</th>
                                  <!-- Các cột khác ở đây -->
                              </tr>
                               
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($supplies as $index => $supply)
                                <tr>
                                    <td style="text-align: center">{{ $index + 1 }}</td>
                                    <td style="text-align: center">{{ $supply->sodonhang }}</td>
                                    <td style="text-align: center">{{ $supply->tenvattu }}</td>
                                    <td style="text-align: center">{{ $supply->nhacungcap }}</td>
                                    <td style="text-align: center">{{ $supply->noidungphancum }}</td>
                                    <td style="text-align: center">{{ $supply->maso }}</td>
                                    <td style="text-align: center">{{ $supply->donvitinh }}</td>
                                    <td style="text-align: center; color: red">{{ $supply->soluong }}</td>
                                    <td style="text-align: center"></td>
                                    <td style="text-align: center"></td>
                                    <td style="text-align: center">{{ $supply->ngaynhan }}</td>
                                    <td style="text-align: center">{{ $supply->chiphi }}</td>
                                    <td style="text-align: center">
                                      {!! DNS1D::getBarcodeHTML($supply->maso, 'C128', 1, 33) !!}
                                    </td>
                                    <td style="text-align: center">
                                      @if ($supply->status == 0)
                                        <span class="badge bg-secondary">Chưa nhận</span>
                                      @endif
                                       
                                    </td>
                                    <td style="text-align: center">{{ $supply->note }}</td>
                                    <td style="text-align: center">
                                      <button onclick="printBarcode('{{$supply->barcodeData}}')">In Barcode</button>
                                    </td>
                                </tr>
                            @endforeach
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
                      <a href="{{ asset('bieumau/file mẫu.xlsx') }}" download>
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
<script>
      function printBarcode(barcodeData) {
          var mywindow = window.open('', 'PRINT', 'height=400,width=600');
      
          mywindow.document.write('<html><head><title>' + 'Barcode' + '</title>');
          mywindow.document.write('</head><body >');
          mywindow.document.write('<h1>' + 'Barcode' + '</h1>');
          mywindow.document.write(document.getElementById(barcodeData).innerHTML);
          mywindow.document.write('</body></html>');
      
          mywindow.document.close(); // necessary for IE >= 10
          mywindow.focus(); // necessary for IE >= 10*/
      
          mywindow.print();
          mywindow.close();
      
          return true;
      }
</script>
@endsection
