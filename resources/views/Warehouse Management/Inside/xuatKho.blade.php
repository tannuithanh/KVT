@extends('Layout.app')
@section('style')
<style>
    .fixed-header th {
        position: sticky;
        top: 0;
        background-color: #fff; /* Để thẻ th có nền trắng */
        z-index: 10;
    }
</style>

@endsection
@section('title')
    Xuất kho
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
                      <span style="font-size: 18px;font-weight: 600;color: #012970;">Dự án: <span  style="color: black">{{ $project->name }}</span>
                    </h5>
                    <div class="filter-box mt-3">
                        <div class="row">
                            <div class="col-6 col-md-2">
                                <input type="text" class="form-control timkiemvattu" placeholder="🔎 Tìm kiếm vật tư" data-id="{{ $project->id }}">
                            </div>
                            <div class="col-6 col-md-2">
                                <button type="button" class="btn btn-outline-primary" id="chonvattu"><i class="bi bi-folder-plus"></i> Chọn vật tư</button>
                                <a href="{{ route('xuatKhoBarcode') }}" class="btn btn-outline-primary bi bi-upc-scan"> Quét Mã </a>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive" style="max-height: 550px;">
                        <table class="table table-borderless table-bordered table-hover mt-2 vattuchitiet fixed-header">
                            <thead>
                                    <tr>
                                        <th style="text-align: center;display:none;" scope="col"> Chọn </th>
                                        <th style="text-align: center" scope="col">Stt</th>
                                        <th style="text-align: center" scope="col">Đơn hàng</th>
                                        <th style="text-align: center" scope="col">Tên vật tư</th>
                                        <th style="text-align: center" scope="col">Mã vật tư</th>
                                        <th style="text-align: center" scope="col">số lượng còn</th>
                                        <th style="text-align: center" scope="col">Chi Phí</th>
                                        <th style="text-align: center" scope="col">Ghi chú</th>
                                    </tr>
                            </thead>
                            <tbody>
                                @php $stt = 1; @endphp
                                @forelse ($orders as $order)
                                    @forelse ($order->supplies as $supply)
                                        <tr>
                                            <td style="text-align: center;display:none;">
                                                <input type="checkbox" value="{{$supply->id}}" class="form-check-input select-checkbox" data-soluongcon="{{$supply->soluong_conlai}}">
                                            </td>
                                            <td style="text-align: center">{{ $stt++ }}</td>
                                            <td style="text-align: center">{{ $order->sodonhang }}</td>
                                            <td style="text-align: center">{{ $supply->tenvattu }}</td>
                                            <td style="text-align: center">{{ $supply->maso }}</td>
                                            <td style="text-align: center">{{ $supply->soluong_conlai }}</td>
                                            <td style="text-align: center">{{ $order->chiphi }}</td>
                                            <td style="text-align: center">{{ $order->ghichu }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" style="text-align: center;vertical-align: middle;">Không có vật tư</td>
                                        </tr>
                                    @endforelse
                                @empty
                                    <tr>
                                        <td colspan="7" style="text-align: center;vertical-align: middle;">Không có dữ liệu</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <button type="button" style="display: none" class="btn btn-outline-primary mt-2" id="inpdf" ><i class="bi bi-file-earmark-pdf"></i> In </button>
                    <button id="thucong" class="btn btn-outline-primary bi bi-mouse2 mt-2"> Xuất thủ công</button>
                </div>
            </div>
        </div>
    </div>
<div class="modal fade" id="printModal" tabindex="-1" aria-labelledby="printModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="printModalLabel">Thông tin in</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>
            <div class="modal-body">
                <div class="form-group">
                  <label for="soInput">Số:</label>
                  <input type="text" class="form-control" id="soInput" placeholder="Nhập số">
                </div>
                <div class="form-group">
                  <label for="donViNhan">Đơn vị nhận:</label>
                  <input type="text" class="form-control" id="donViNhan" placeholder="Nhập đơn vị nhận">
                </div>
                <div class="form-group">
                    <label for="donViNhan">Mục đích xuất:</label>
                    <input type="text" class="form-control" id="mucdichxuat" placeholder="Mục đích xuất">
                </div>
                <div class="form-group">
                    <label for="donViNhan">Vật tư thương hiệu xuất:</label>
                    <input type="text" class="form-control" id="vattuthuonghieuxuat" placeholder="Vật tư thương hiệu xuất">
                </div>
                <div class="form-group">
                    <label for="donViNhan">Người nhận: </label>
                    <input type="text" class="form-control" id="nguoinhan" placeholder="Người nhận">
                </div>
                <div class="form-group">
                    <label for="donViNhan">Người cấp:</label>
                    <input type="text" class="form-control" id="nguoicap" placeholder="Người cấp">
                </div>
                <div class="form-group">
                    <label for="donViNhan">Người lập:</label>
                    <input type="text" class="form-control" id="nguoilap" placeholder="Người lập">
                </div>
                <div class="form-group">
                    <label for="donViNhan">Ngày tạo phiếu:</label>
                    <input type="date" class="form-control" id="ngaytaophieu" placeholder="Người lập">
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Đóng</button>
              <button type="button" class="btn btn-primary" id="printButton">In</button>

            </div>
          </div>
        </div>
</div>
@endsection
@section('script')
<script>
    // Mảng để lưu trữ các ID của vật tư đã được chọn
    var selectedSupplies = [];

        $(document).ready(function() {
            $(document).on('change', '.select-checkbox', function() {
                // Gọi hàm kiểm tra trạng thái checkbox và cập nhật nút In PDF
                updatePdfButtonState();
            });
            $('#chonvattu').click(function() {
                $('th:first-child, td:first-child').toggle();
                isColumnVisible = !isColumnVisible;
            });


            // Gắn sự kiện thay đổi cho các checkbox trên bảng, sử dụng event delegation
            $(document).on('change', '.select-checkbox', function() {
                var soluongCon = $(this).data('soluongcon');
                if (soluongCon <= 0) {
                    // Sử dụng SweetAlert2 để hiển thị thông báo
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi...',
                        text: 'Không có vật tư này trong kho.',
                    });
                    // Dùng return false để ngăn không cho checkbox được chọn
                    $(this).prop('checked', false);
                    return false;
                }

                var supplyId = $(this).val();
                if ($(this).is(':checked')) {
                    selectedSupplies.push(supplyId);
                } else {
                    selectedSupplies = selectedSupplies.filter(function(id) {
                        return id !== supplyId;
                    });
                }
            });

            // Sự kiện keyup khi tìm kiếm vật tư
            $('.timkiemvattu').keyup(function() {
                var searchText = $(this).val();
                var projectId = $(this).data('id');
                $.ajax({
                    url: "{{ route('searchSuppliesReal') }}",
                    type: 'GET',
                    data: {
                        keyword: searchText,
                        project_id: projectId
                    },
                    success: function(data) {
                        // Cập nhật lại tbody của bảng với dữ liệu mới
                        var newBodyHtml = '';
                        data.forEach(function(item, index) {
                            newBodyHtml += `<tr>
                                                <td style="text-align: center;display:none;">
                                                    <input type="checkbox" class="form-check-input select-checkbox" value="${item.id}" ${selectedSupplies.includes(item.id.toString()) ? 'checked' : ''}>
                                                </td>
                                                <td style="text-align: center;">${index + 1}</td>
                                                <td style="text-align: center;">${item.sodonhang || ''}</td>
                                                <td style="text-align: center;">${item.tenvattu}</td>
                                                <td style="text-align: center;">${item.maso}</td>
                                                <td style="text-align: center;">${item.soluong_conlai}</td>
                                                <td style="text-align: center;">${item.chiphi}</td>
                                                <td style="text-align: center;">${item.ghichu ? item.ghichu : ''}</td>
                                            </tr>`;
                        });
                        $('.vattuchitiet tbody').html(newBodyHtml);

                        if($('td.select-checkbox').is(':visible')) {
                            $('td.select-checkbox').show();

                        }
                    }
                });
            });

            function updatePdfButtonState() {
                var checkedCount = $('.select-checkbox:checked').length;
                if (checkedCount > 0) {
                    $('#inpdf, #thucong').show();
                } else {
                        $('#inpdf, #thucong').hide();
                }
            }
                updatePdfButtonState();
            });
</script>
<script>
    $(document).ready(function() {
        $('#inpdf').click(function() {
            $('#printModal').modal('show');
        });

        $('#printButton').click(function() {
            var data = {
                projectId: {{$project->id}},
                so: $('#soInput').val(),
                donViNhan: $('#donViNhan').val(),
                mucDichXuat: $('#mucdichxuat').val(),
                vattuThuongHieuXuat: $('#vattuthuonghieuxuat').val(),
                nguoiNhan: $('#nguoinhan').val(),
                nguoiCap: $('#nguoicap').val(),
                nguoiLap: $('#nguoilap').val(),
                ngayTaoPhieu: $('#ngaytaophieu').val(),
                selectedSupplies: selectedSupplies
            };
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('formTrinhKy')}}",
                type: 'POST',
                data: data,
                success: function(response) {
                    console.log('Dữ liệu đã được gửi thành công', response);
                    window.location.href = response.redirectUrl;
                },
                error: function(xhr, status, error) {
                    // Xử lý lỗi
                    console.log('Có lỗi xảy ra: ' + error);
                }
            });

            $('#printModal').modal('hide');
        });
    });




</script>


@endsection
