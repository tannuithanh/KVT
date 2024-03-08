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
                                            <th style="text-align: center" rowspan="2" scope="col">Chi Phí</th>
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
{{-- MODAL DANH MỤC VẬT TƯ--}}
    <div class="modal fade" id="danhMucVatTuChiTiet" tabindex="-1">
        <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Vật tư chi tiết</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
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
            <table class="table table-bordered table-hover danhmucvattuchitiet">
                <thead>
                <tr>
                    <th rowspan="2" style="text-align: center; vertical-align: middle;">STT</th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Tên vật tư</th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Mã số</th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Đơn vị tính</th>
                    <th colspan="4" style="text-align: center;">Tình trạng</th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Mã Boardcode</th>
                    <th rowspan="2" style="text-align: center; vertical-align: middle;">Thao tác</th>
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
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th rowspan="2" style="text-align: center; vertical-align: middle;">STT</th>
                            <th rowspan="2" style="text-align: center; vertical-align: middle;">Tên vật tư</th>
                            <th rowspan="2" style="text-align: center; vertical-align: middle;">Mã số</th>
                            <th rowspan="2" style="text-align: center; vertical-align: middle;">Loại giao dịch</th>
                            <th colspan="4" style="text-align: center;">Ngày giao dịch</th>
                            <th rowspan="2" style="text-align: center; vertical-align: middle;">Ghi chú</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>

@endsection

@section('script')
  <script>
    $('.btn-secondary').click(function() {
        var supplyId = $(this).data('id'); // Lấy ID từ data-id của nút
        $('#supplyId').val(supplyId); // Đặt ID vào trường ẩn
    });
  </script>
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
                                  // console.log(item);
                                  if (item) {
                                      var buttonsHtml = `<td style="text-align:center;vertical-align: middle" class="action-btn"></td>`;
                                        var row = '<tr id="supply-row-' + item.id + '" data-id="' + item.id + '">' +
                                          '<td style="text-align:center;vertical-align: middle">' + (index + 1) + '</td>' +
                                          '<td style="text-align:center;vertical-align: middle" class="tenvattu">' + (item.tenvattu) + '</td>' +
                                          '<td style="text-align:center;vertical-align: middle" class="maso">' + (item.maso) + '</td>' +
                                          '<td style="text-align:center;vertical-align: middle" class="donvitinh">' + (item.donvitinh) + '</td>' +
                                          '<td style="text-align:center;vertical-align: middle" class="soluong">' + (item.soluong) + '</td>' +
                                          '<td style="text-align:center;vertical-align: middle" class="danhan">' + (item.danhan) + '</td>' +
                                          '<td style="text-align:center;vertical-align: middle" class="chuanhan">' + (item.chuanhan) + '</td>' +
                                          '<td style="text-align:center;vertical-align: middle" class="daxuat">' + (item.daxuat) + '</td>' +
                                          '<td class="barcode">' + (item.barcodeHtml || '') + '<div>' + (item.maso || '') + '</div></td>' +
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
                            htmlContent += `<tr>
                                                <td style="text-align: center; vertical-align: middle;">${index + 1}</td>
                                                <td style="text-align: center; vertical-align: middle;">${transaction.tenvattu}</td>
                                                <td style="text-align: center; vertical-align: middle;">${transaction.maso}</td>
                                                <td style="text-align: center; vertical-align: middle;">${transaction.loaigiaodich}</td>
                                                <td colspan="4" style="text-align: center; vertical-align: middle;">${transaction.ngaygiaodich}</td>
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

@endsection
