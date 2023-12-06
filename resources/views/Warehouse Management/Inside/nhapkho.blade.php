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
        <li class="breadcrumb-item">Nhập kho</li>
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
                    <h5 class="mt-2" style="font-size: 18px;font-weight: 600;color: #012970;">Danh sách vật tư</h5>
                    <h5 style="font-size: 18px;font-weight: 600;color: #012970;">Thương hiệu: <span style="color: red">{{$project->brand->name}}</span></h5>
                    <h5 style="font-size: 18px;font-weight: 600;color: #012970;">Dự án: <span  style="color: red">{{$project->name}}</span></h5>
                    <button type="button" class="btn btn-outline-success ri-add-line" data-bs-toggle="modal" data-bs-target="#themdanhmucvattu"> Thêm vật tư</button>
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

{{-- MODEL --}}
<div class="modal fade" id="themdanhmucvattu" tabindex="-1" style="display: none;" aria-modal="false" role="dialog">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Thêm vật tư</h5>
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
            <input type="file" class="mt-2" id="file-upload" multiple required />
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="button" class="btn btn-primary saveSupplies">Lưu</button>
        </div>
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
@endsection
