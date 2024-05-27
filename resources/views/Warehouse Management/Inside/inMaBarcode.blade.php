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
    <section class="section">
        <div class="row">
            <div class="col-lg-6">
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
                            <!-- Phần hiển thị kết quả -->
                            <div class="col-md-9 mt-2">
                                <div class="row" id="searchResultsContainer" style="display: none;">
                                    <!-- Kết quả tìm kiếm sẽ được chèn vào đây -->
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
                        var container = $("#searchResultsContainer");
                        container.empty(); // Xóa nội dung cũ

                        // Duyệt qua mảng dữ liệu trả về và thêm vào container
                        $.each(response.data, function(index, item) {
                            console.log(item)
                            var cardHtml = `
                                    <div class="col-md-12 mb-3">
                                            <div class="card" style="border: 2px solid #007bff; border-radius: 10px; padding: 10px;">
                                                <div class="card-body" style="padding: 0px 20px 0px 25px !important">
                                                    <div class="row">
                                                        <table class="table table-bordered">
                                                            <tr>
                                                                <td><strong>Mã số:</strong></td>
                                                                <td>${item.maso}</td>
                                                                <td rowspan="4" class="text-center align-middle" style="width: 120px;">
                                                                    <div class="qr-code">${item.qrCode}</div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Tên vật Tư:</strong></td>
                                                                <td>${item.tenvattu}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Số đơn hàng:</strong></td>
                                                                <td>${item.donhang}</td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mt-2">
                                                    <label for="quantity-${index}">Số lượng in:</label>
                                                    <input type="number" id="quantity-${index}" class="form-control" value="1" min="1" />
                                            </div>
                                    </div>`;
                            container.append(cardHtml);
                        });

                        $('#btnInMaBarcode').show();
                        $('#searchResultsContainer').show();
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
            var inputs = $("#searchResultsContainer input[type='number']");
            var allEmpty = true;

            var iframe = $('<iframe id="printFrame" style="display:none"></iframe>').appendTo('body')[0];
            var doc = iframe.contentDocument || iframe.contentWindow.document;
            doc.open();
            doc.write('<html><head><title>In Barcode</title>');
            doc.write('<style>');
            doc.write('@page { size: auto; margin: 10mm; }');
            doc.write('body { font-family: Arial, sans-serif; }');
            doc.write('.card {}');
            doc.write('.card-body { padding: 0px 20px 0px 25px !important; }');
            doc.write('.table { width: 100%; border-collapse: collapse; }');
            doc.write('.table-bordered td { border: 1px solid #dee2e6; padding: 5px; vertical-align: middle; }');
            doc.write('.text-center { text-align: center; }');
            doc.write('.align-middle { vertical-align: middle; }');
            doc.write('</style></head><body>');

            inputs.each(function() {
                var quantity = $(this).val(); // Số lượng từ input
                if (quantity > 0) {
                    allEmpty = false;
                    var $card = $(this).closest('.col-md-12');
                    var maso = $card.find('td').eq(1).text(); // Giả sử mã số là cột thứ 2
                    var tenvattu = $card.find('td').eq(4).text(); // Giả sử tên vật tư là cột thứ 3
                    var donhang = $card.find('td').eq(6).text(); // Giả sử số đơn hàng là cột thứ 1
                    var qrCode = $card.find('.qr-code').html(); // Giả sử qrCode là cột thứ 4

                    for (var i = 0; i < quantity; i++) {
                        doc.write('<div class="card">');
                        doc.write('<div class="card-body">');
                        doc.write('<div class="row">');
                        doc.write('<table class="table table-bordered">');
                        doc.write('<tr>');
                        doc.write('<td><strong>Mã số:</strong></td>');
                        doc.write('<td>' + maso + '</td>');
                        doc.write('<td rowspan="3" class="text-center align-middle" style="width: 120px;"><div class="qr-code">' + qrCode + '</div></td>');
                        doc.write('</tr>');
                        doc.write('<tr>');
                        doc.write('<td><strong>Tên vật tư:</strong></td>');
                        doc.write('<td>' + tenvattu + '</td>');
                        doc.write('</tr>');
                        doc.write('<tr>');
                        doc.write('<td><strong>Số đơn hàng:</strong></td>');
                        doc.write('<td>' + donhang + '</td>');
                        doc.write('</tr>');
                        doc.write('</table>');
                        doc.write('</div>');
                        doc.write('</div>');
                        doc.write('</div>');
                    }
                }
            });

            doc.write('</body></html>');
            doc.close();

            if (!allEmpty) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
                $(iframe).remove();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi...',
                    text: 'Bạn hãy nhập số lượng in vào!',
                });
            }
        });
    });
</script>





@endsection
