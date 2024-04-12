@extends('Layout.app')
@section('style')
<link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" />
<style>
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
    }
    @media (max-width: 768px) {
        .select2-container {
            width: 100% !important;
        }
        .select2-dropdown {
            width: auto !important;
        }
    }
</style>
@endsection
@section('title')
In mã barcode
@endsection
@section('content')
<div class="pagetitle">
    <h1>In mã barcode</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Trang chủ</a></li>
            <li class="breadcrumb-item">In mã barcode</li>
        </ol>
    </nav>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <!-- Phần nhập liệu -->
                        <div class="col-md-3 mt-2">
                            <!-- Select 1 -->
                            <div class="row mb-2">
                                <div class="col-md-12">
                                    <select class="form-control select2" name="maso" id="maso" placeholder="Chọn một lựa chọn">
                                        @foreach ($supplies as $supply)
                                            <option value="{{ $supply->maso }}">{{ $supply->maso }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-primary" id="btnTimKiem"><i class="bi bi-search"></i> Tìm kiếm</button>
                                </div>
                            </div>
                        </div>
                        <!-- Phần hiển thị bảng -->
                        <div class="col-md-9 mt-2">
                            <div class="table-responsive">
                                <table class="table table-bordered bangchitiet">
                                    <thead>
                                        <tr>
                                            <th>Đơn Hàng</th>
                                            <th>Tên vật tư</th>
                                            <th>Mã số</th>
                                            <th>Barcode</th>
                                            <th>Số lượng In</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" style="text-align:center">Tìm kiếm vật tư</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button id="btnInMaBarcode" class="btn btn-primary" style="display: none;">
                                <i class="bi bi-printer"></i> In mã
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
@section('script')
<script src="{{asset('assets/js/select2.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });
</script>

<script>
    $(document).ready(function() {
        $('#btnTimKiem').click(function() {
            var maso = $('#maso').val(); // Lấy giá trị mã số được chọn

            // Gửi yêu cầu AJAX
            $.ajax({
                url: "{{ route('timKiemMaBarcode') }}",
                type: "POST",
                data: {
                    maso: maso,
                    _token: "{{ csrf_token() }}" // CSRF token là bắt buộc
                },
                success: function(response) {
                    // Xử lý kết quả trả về
                    if(response && response.data) {
                        var tbody = $(".bangchitiet tbody");
                        tbody.empty(); // Xóa nội dung cũ của bảng

                        // Duyệt qua mảng dữ liệu trả về và thêm vào bảng
                        $.each(response.data, function(index, item) {
                            tbody.append(
                                `<tr>
                                    <td style="text-align: center; vertical-align: middle;">${item.donhang}</td>
                                    <td style="text-align: center; vertical-align: middle;">${item.maso}</td>
                                    <td style="text-align: center; vertical-align: middle;">${item.tenvattu}</td>
                                    <td style=" vertical-align: middle;">${item.barcode}${item.maso}</td>
                                    <td style="text-align: center; vertical-align: middle;">
                                        <input type="number" class="form-control" style="width: 100px; margin: auto;" value="${item.soluongin}" />
                                    </td>
                                </tr>`
                            );
                        });
                        $('#btnInMaBarcode').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error: " + status + " " + error);
                }
            });
        });
    });
</script>

<script>
    $(document).ready(function() {
        $('#btnInMaBarcode').click(function() {
            var inputs = $(".bangchitiet tbody input[type='number']");
            var allEmpty = true;

            var iframe = $('#printFrame').get(0);
            var doc = iframe.contentDocument || iframe.contentWindow.document;
            doc.open();
            doc.write('<html><head><title>In Barcode</title>');
            doc.write('<style>');
            doc.write('body { font-family: Arial, sans-serif; font-size: 8pt; }');
            doc.write('.print-table { width: 100%; border-collapse: collapse; page-break-after: always; }');
            doc.write('td, th { border: 1px solid #ddd; text-align: left; padding: 8px; font-size: 8pt; }');
            doc.write('.ten-vat-tu { max-width: 200px; word-wrap: break-word; font-size: 8pt; }');
            doc.write('</style></head><body>');

            inputs.each(function() {
                var quantity = $(this).val(); // Số lượng từ input
                if (quantity > 0) {
                    allEmpty = false;
                    var $row = $(this).closest('tr');
                    var maso = $row.find('td:eq(1)').text(); // Giả sử mã số là cột thứ 2
                    var tenvattu = $row.find('td:eq(2)').text(); // Giả sử tên vật tư là cột thứ 3
                    var barcode = $row.find('td:eq(3)').html(); // Giả sử barcode là cột thứ 4

                    for (var i = 0; i < quantity; i++) {
                        doc.write('<table class="print-table"><tbody>');
                        doc.write(`<tr>
                                        <td>${tenvattu}</td>
                                        <td>${barcode}${maso}</td>
                                    </tr>`);
                        doc.write('</tbody></table>');
                    }
                }
            });

            doc.write('</body></html>');
            doc.close();

            if (!allEmpty) {
                // In nội dung
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            } else {
                // Hiển thị thông báo yêu cầu nhập số lượng
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Bạn hãy nhập số lượng in vào!',
                });
            }
        });
    });
</script>

@endsection
