@extends('Layout.app')
@section('style')
<link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" />
  <style>
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
    Quản lý đơn hàng
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
                        <button class="btn btn-outline-primary" type="button" data-bs-target="#themdanhmucvattubangfileexcel" data-bs-toggle="modal">
                            + Đơn hàng và vật tư
                        </button>
                        {{-- <button class="btn btn-outline-primary" type="button" data-bs-target="#themdonhangthucong" data-bs-toggle="modal">
                            + Đơn hàng thủ công
                        </button> --}}
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
                                        <th style="text-align: center" colspan="5" scope="col">Tình trạng</th>
                                        <th style="text-align: center" rowspan="2" scope="col">Chi Phí</th>
                                        <th style="text-align: center" rowspan="2" scope="col">Ghi chú</th>
                                        <th style="text-align: center" rowspan="2" scope="col">Thao tác</th>
                                    </tr>
                                    <tr>

                                            <th style="text-align: center" scope="col">Tổng</th>
                                            <th style="text-align: center" scope="col">Đã nhận</th>
                                            <th style="text-align: center" scope="col">Lưu kho</th>
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
                                            <td style="text-align: center;vertical-align: middle;">-</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->total_danhan ?? '0' }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->total_chuanhan ?? '0' }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->total_daxuat ?? '0' }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->chiphi }}</td>
                                            <td style="text-align: center;vertical-align: middle;">{{ $order->ghichu }}</td>
                                            <td class="no-modal-trigger" style="text-align: center;vertical-align: middle;">
                                              <button class="btn btn-sm btn-danger delete-supplies" data-id="{{$order->id}}">Xóa</button>
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
              <form action="{{ route('listWarehouse', ['project' => $project->id]) }}" method="GET">
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
{{-- SỬA ĐƠN HÀNG THỦ CÔNG --}}
  {{-- <div class="modal fade" id="EditSupperlies" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Sửa đơn hàng</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body ">
          <form action="{{route('suavattu')}}" class="row g-3" method="POST">
            @csrf
            <input id="OrderEditDonHang" name="OrderEditDonHang" hidden>
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
  </div> --}}
{{-- MODAL DANH MỤC VẬT TƯ--}}
    <div class="modal fade" id="danhMucVatTuChiTiet" tabindex="-1">
        <div class="modal-dialog modal-xl">
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
                <div class="table-responsive" style="max-height: 400px;">
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
{{-- SỬA VẬT TƯ THỦ CÔNG --}}
    <div class="modal fade" style="background-color: #000000bb" id="suavattuthucong" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Sửa vật tư</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ">
                    <div class="col-md-12">
                        <label for="inputName5" class="form-label">Tên vật tư</label>
                        <input type="text" class="form-control" name="tenvattu-edit">
                    </div>
                    <div class="col-md-12">
                        <label for="inputName5" class="form-label">Mã số vật tư</label>
                        <input type="text" class="form-control" name="maso-edit">
                    </div>
                    <div class="col-md-12">
                        <label for="inputState" class="form-label">Đơn vị tính</label>
                        <select id="inputState" class="form-select" name="donvitinh-edit">
                            <option value="CÁI">CÁI</option>
                            <option value="BỘ">BỘ</option>
                            <option value="CT">CT</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label for="inputName5" class="form-label">số lượng</label>
                        <input type="number" class="form-control" name="soluongvattuthemvao-edit">
                    </div>
                    <div class="col-md-12">
                        <label for="inputName5" class="form-label">ghi chú</label>
                        <textarea type="number" class="form-control" name="ghichuvattuchitiet-edit"></textarea>
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-primary" id="suavattu-edit">Sửa vật tư</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
{{-- THÊM VẬT TƯ THỦ CÔNG --}}
    {{-- <div class="modal fade" style="background-color: #000000bb" id="themvattuthucong" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Thêm vật tư vật tư</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body ">
                    <div class="col-md-12">
                        <label for="inputName5" class="form-label">Tên vật tư</label>
                        <input type="text" class="form-control" name="tenvattu">
                    </div>
                    <div class="col-md-12">
                        <label for="inputName5" class="form-label">Mã số vật tư</label>
                        <input type="text" class="form-control" name="maso">
                    </div>
                    <div class="col-md-12">
                        <label for="inputState" class="form-label">Đơn vị tính</label>
                        <select id="inputState" class="form-select" name="donvitinh">
                            <option value="CÁI">CÁI</option>
                            <option value="BỘ">BỘ</option>
                            <option value="CT">CT</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label for="inputName5" class="form-label">số lượng</label>
                        <input type="number" class="form-control" name="soluongvattuthemvao">
                    </div>
                    <div class="col-md-12">
                        <label for="inputName5" class="form-label">ghi chú</label>
                        <textarea type="number" class="form-control" name="ghichuvattuchitiet"></textarea>
                    </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button class="btn btn-primary" id="themvattu">Thêm vật tư</button>
                    </div>
            </div>
        </div>
    </div> --}}
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
{{-- THÊM ĐƠN HÀNG THỦ CÔNG --}}
    {{-- <div class="modal fade" id="themdonhangthucong" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Thêm đơn hàng</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body ">
            <form action="{{ route('themdonhangthucong') }}" class="row g-3" method="POST">
                @csrf
                <input id="OrderEdit" name="project_id" value="{{$project->id}}" hidden>
                <div class="col-md-12">
                    <label for="inputName5" class="form-label">Số đơn hàng</label>
                    <input type="text" class="form-control" name="sodonhang">
                </div>
                <div class="col-md-12">
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
                <button class="btn btn-primary" type="submit">Thêm đơn hàng</button>
                </div>
            </form>
        </div>
        </div>
    </div> --}}
@endsection
{{-- <button class="btn btn-sm btn-primary chinhsuavattuchitiet"
    data-orderId="${orderId}"
    data-id="${item.id}"
    data-tenvattu="${item.tenvattu}"
    data-maso="${item.maso}"
    data-donvitinh="${item.donvitinh}"
    data-soluong="${item.soluong}">Sửa</button> --}}
@section('script')
<script src="{{asset('assets/js/select2.min.js')}}"></script>
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
    {{-- <script>
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


    </script> --}}
  {{-- HIỂN THỊ MODAL VẬT TƯ CHI TIẾT --}}
    <script>
            $(document).ready(function() {
                //HIỂN THỊ VẬT TƯ CHI TIẾT
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
                                _token: '{{ csrf_token() }}' // Đảm bảo bạn thêm token CSRF
                            },
                            success: function(data) {

                                var tbody = $('#danhMucVatTuChiTiet').find('tbody');
                                tbody.empty(); // Xóa nội dung hiện tại của tbody
                                $('#themvattuchitiet').attr('data-id', orderId);
                                $('#timkiemVatTuChiTiet').attr('data-id', orderId);
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
                                        var buttonsHtml = `<td style="text-align:center;vertical-align: middle" class="action-btn">

                                            <button class="btn btn-sm btn-danger xoavattuchitiet"
                                                data-id="${item.id}">Xóa</button>
                                            </td>`;

                                            var row = '<tr id="supply-row-' + item.id + '" data-id="' + item.id + '">' +
                                            '<td style="text-align:center;vertical-align: middle">' + (index + 1) + '</td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="tenvattu">' + (item.tenvattu) + '</td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="maso">' + (item.maso) + '</td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="donvitinh">' + (item.donvitinh) + '</td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="soluong">' + (item.soluong) + '</td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="soluong"> - </td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="danhan">' + (item.danhan) + '</td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="chuanhan">' + (item.chuanhan) + '</td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="daxuat">' + (item.daxuat) + '</td>' +
                                            '<td class="barcode">' + (item.barcodeHtml || '') + '<div>' + (item.maso || '') + '</div></td>' +
                                            '<td style="text-align:center;vertical-align: middle" class="ghichu">' + (item.ghichu !== null ? item.ghichu : '') + '</td>' +
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
                                                <td style="text-align: center; vertical-align: middle;">${transaction.ghichu}</td>
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
                                var buttonsHtml = `<td style="text-align:center;vertical-align: middle" class="action-btn">
                                                    <button class="btn btn-sm btn-primary chinhsuavattuchitiet"
                                                        data-id="${supply.id}"
                                                        data-tenvattu="${supply.tenvattu}"
                                                        data-maso="${supply.maso}"
                                                        data-donvitinh="${supply.donvitinh}"
                                                        data-soluong="${supply.soluong}">Sửa</button>
                                                    <button class="btn btn-sm btn-danger xoavattuchitiet"
                                                        data-id="${supply.id}">Xóa</button>
                                                    </td>`;
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
                                        ${buttonsHtml}
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
