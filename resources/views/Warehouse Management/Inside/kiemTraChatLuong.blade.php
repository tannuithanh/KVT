@extends('Layout.app')
@section('style')
<link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" />
<style>
    .filter-box {
        border: 1px solid #173e864f; /* Màu border, có thể điều chỉnh */
        padding: 11px;
        margin-bottom: 20px; /* Khoảng cách với nội dung tiếp theo */
        border-radius: 5px; /* Bo góc cho khung */
    }
    .viewport {
        width: 100%;
        height: 400px;
        position: relative;
        border: 1px solid #ccc;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.5);
    }
</style>
@endsection
@section('title')
    Kiểm tra chất lượng
@endsection

@section('content')
<div class="pagetitle">
    <h1>Kiểm tra chất lượng</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Trang chủ</a></li>
            <li class="breadcrumb-item">Kiểm tra chất lượng</li>

        </ol>
    </nav>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mt-2" style="font-size: 18px;font-weight: 600;color: #012970;">
                    </h5>
                    <div class="filter-box mt-3">
                        <div class="row">
                            <div class="col-6 col-md-2" >
                                <input class="form-select ngaykiemtra"  type="date">
                            </div>
                            <div class="col-6 col-md-2" >
                                <select class="form-select donvitinhchitiet status" aria-label="Default select example" >
                                    <option value="1">Đã kiểm tra</option>
                                    <option value="0">Chưa kiểm tra</option>
                                </select>
                            </div>
                            <div class="col-6 col-md-2">
                                <button type="submit" id="timkiemVatTuChiTiet" class="btn btn-primary" style="margin-left: -5px">Tìm kiếm</button>
                            </div>
                        </div>
                    </div>
                          <div class="table-responsive" style="max-height: 600px;">
                            <table class="table table-borderless table-bordered sticky-header nhapkhokiemtra">
                                <thead>
                                    <tr>
                                        <th style="text-align: center" scope="col">Stt</th>
                                        <th style="text-align: center" scope="col">Đơn hàng</th>
                                        <th style="text-align: center" scope="col">Tên vật tư</th>
                                        <th style="text-align: center" scope="col">Mã số</th>
                                        <th style="text-align: center" scope="col">Số lượng nhập</th>
                                        <th style="text-align: center" scope="col">Số lượng đạt</th>
                                        <th style="text-align: center" scope="col">Tình trạng</th>
                                        <th style="text-align: center" scope="col">Ngày kiểm tra</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $stt = 1;
                                    @endphp
                                    @forelse ($qualityChecks as $item)
                                        <tr data-id="{{ $item->id }}" data-maso="{{ $item->supply->maso }}" data-tenvattu="{{ $item->supply->tenvattu }}" data-status="{{$item->status}}">
                                            <td style="text-align: center">{{ $stt++ }}</td>
                                            <td style="text-align: center">{{ $item->supply->order->sodonhang ?? 'N/A' }}</td>
                                            <td style="text-align: center">{{ $item->supply->tenvattu }}</td>
                                            <td style="text-align: center">{{ $item->supply->maso }}</td>
                                            <td style="text-align: center">{{ $item->soluongnhapkho }}</td>
                                            <td style="text-align: center">{{ $item->soluongdatchatluong ?? '' }}</td>
                                            <td style="text-align: center; background-color: {{ $item->status == 0 ? 'yellow' : 'green' }}">{{ $item->status == 0 ? 'Chưa kiểm tra' : 'Đã kiểm tra' }}</td>
                                            <td style="text-align: center">{{ $item->ngaykiemtra ?? '' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" style="text-align: center" scope="col">Không có vật tư cần kiểm tra</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        <a href="{{ route('kiemTraCLBarcode') }}" class="btn btn-outline-primary bi bi-upc-scan mt-2"> Quét Mã</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')
        <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
        <script src="{{asset('assets/js/select2.min.js')}}"></script>
    {{--TÌM KIẾM VẬT TƯ--}}
        <script>
            $(document).ready(function() {
                $('#timkiemVatTuChiTiet').click(function() {
                    // Lấy giá trị từ các input và select box
                    var ngayKiemTra = $('.ngaykiemtra').val();
                    var status = $('.status').val(); // Cập nhật selector nếu cần
                    $.ajax({
                        url: "{{ route('timKiemVatTuCheck') }}",
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            status: status,
                            ngayKiemTra: ngayKiemTra
                        },
                        success: function(response) {
                            console.log(response)
                                // Kiểm tra nếu yêu cầu thành công và có dữ liệu trả về
                                if(response.success && response.data.length > 0) {
                                    var tbodyContent = ''; // Chuỗi HTML cho nội dung mới của tbody
                                    var stt = 1; // Số thứ tự

                                    response.data.forEach(function(item) {
                                        // Định dạng ngày kiểm tra
                                        var ngaykiemtraFormatted = '';
                                        if (item.ngaykiemtra) {
                                            var date = new Date(item.ngaykiemtra);
                                            var day = ("0" + date.getDate()).slice(-2);
                                            var month = ("0" + (date.getMonth() + 1)).slice(-2);
                                            var year = date.getFullYear();
                                            ngaykiemtraFormatted = day + '/' + month + '/' + year;
                                        }

                                        tbodyContent += '<tr data-id="' + item.id + '" data-maso="' + item.supply.maso + '" data-tenvattu="' + item.supply.tenvattu + '" data-status="' + item.status + '">';
                                        tbodyContent += '<td style="text-align: center">' + (stt++) + '</td>';
                                        tbodyContent += '<td style="text-align: center">' + item.supply.order.sodonhang + '</td>';
                                        tbodyContent += '<td style="text-align: center">' + item.supply.tenvattu + '</td>';
                                        tbodyContent += '<td style="text-align: center">' + item.supply.maso + '</td>';
                                        tbodyContent += '<td style="text-align: center">' + item.soluongnhapkho + '</td>';
                                        tbodyContent += '<td style="text-align: center">' + (item.soluongdatchatluong || '') + '</td>';
                                        tbodyContent += '<td style="text-align: center; background-color:' + (item.status == 1 ? '#00FF00' : '#FFFF00') + '">' + (item.status == 1 ? 'Đã kiểm tra' : 'Chưa kiểm tra') + '</td>';
                                        tbodyContent += '<td style="text-align: center">' + ngaykiemtraFormatted + '</td>';
                                        tbodyContent += '</tr>';
                                    });

                                    // Cập nhật nội dung của tbody trong bảng
                                    $('.nhapkhokiemtra tbody').html(tbodyContent);
                                } else {
                                    // Nếu không có dữ liệu, hiển thị thông báo hoặc làm sạch tbody
                                    $('.nhapkhokiemtra tbody').html('<tr><td colspan="8" style="text-align: center">Không có dữ liệu</td></tr>');
                                }
                            },
                        error: function(xhr, status, error) {
                            // Xử lý lỗi
                            console.error(error);
                        }
                    });
                });
            });
        </script>
@endsection

