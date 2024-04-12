<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">

        <title>In mẫu trình ký</title>
        <meta content="" name="description">
        <meta content="" name="keywords">
        <link rel="icon" href="{{asset('favicon.ico')}}" type="image/x-icon"/>
        <link rel="shortcut icon" href="{{asset('favicon.ico')}}" type="image/x-icon"/>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Favicons -->
        <link href="{{asset('assets/img/logo1.png')}}" rel="icon">
        <link href="{{asset('assets/img/apple-touch-icon.png')}}" rel="apple-touch-icon">
        <link href="{{asset('assets/css/fontquantrong.css')}}"rel="stylesheet">

        <!-- Vendor CSS Files -->
        <link href="{{asset('assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/quill/quill.snow.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/quill/quill.bubble.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/remixicon/remixicon.css')}}" rel="stylesheet">
        <link href="{{asset('assets/vendor/simple-datatables/style.css')}}" rel="stylesheet">
        <link href="{{asset('assets/css/style.css')}}" rel="stylesheet">
        <link href="{{asset('assets/css/loadingPage.css')}}" rel="stylesheet">
        <link href="{{ asset('assets/css/sweetalert2.min.css') }}" rel="stylesheet">
        <link href="{{asset('assets/css/select2.min.css')}}" rel="stylesheet" />


    </head>
    <style>
        .bold {
            font-weight: bold;
            font-family: 'Times New Roman', Times, serif;
        }
        </style>
    <style>
        .signature-space {
            font-family: 'Times New Roman', Times, serif;
            height: 50px; /* Điều chỉnh chiều cao nếu cần */
            margin-bottom: 15px; /* Điều chỉnh khoảng cách giữa dòng ký và tên */
        }
    </style>
<body>
    <iframe name="printFrame" id="printFrame" style="display:none;"></iframe>
      <main id="main" class="main content">
        <div class="modal fade show" id="fullscreenModal" tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
            <div class="modal-dialog modal-fullscreen" >
                <div class="modal-content" style="background: url('{{asset('assets/img/BG.JPG')}}') no-repeat center center; background-size: cover;">
                <div class="modal-header">
                  <h5 class="modal-title">Biểu mẫu trình ký</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" id="Close" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;">
                    <div class="col-md-12">
                        <div id="contentToPrint" class="centered-div" style="background-color: white; margin: auto; width: 50%; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);">
                            {{-- Phần tên biểu mẫu --}}
                                <div class="table-responsive">
                                    <table class="table table-bordered" style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;vertical-align: middle;border: 1px solid black;">
                                        <thead>
                                            <tr>
                                                <th style="text-align: left;vertical-align: middle;width: 260px;"><img src="{{ asset('assets/img/logo.png') }}" alt="logo" style="height: 35px;"></th>
                                                <th style="text-align: center; font-size: 25px; vertical-align: middle;">
                                                    PHIẾU XUẤT KHO
                                                    <br>
                                                    <span style="font-size: 15px;">Số: {{$so}}</span>
                                                </th>
                                                <th style="text-align: center;font-size:16px;vertical-align: middle">QT.RDOT.VTRD-01-BM01</th>
                                            </tr>

                                        </thead>
                                    </table>
                                </div>
                            {{-- Phần thông tin --}}
                                <div class="row mt-3">
                                    <div class="col-6"><strong>Đơn vị nhận:</strong> {{$donViNhan}}</div>
                                    <div class="col-6"><strong>Tên xe/ Mã dự án:</strong> {{$project->name}}</div>
                                </div>
                                <div class="row mt-1">
                                    <div class="col-6"><strong>Mục đích xuất:</strong> {{$mucDichXuat}}</div>
                                    <div class="col-6"><strong>Vật tư thương hiệu xuất:</strong> {{$vattuThuongHieuXuat}}</div>
                                </div>
                            {{-- Phần danh mục vật tư --}}
                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="table-responsive" style="font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;">
                                            <table class="table table-bordered danhmucvattuchitiet" style="border: 1px solid black;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center align-middle">Stt</th>
                                                        <th class="text-center align-middle">Mã số</th>
                                                        <th class="text-center align-middle">Tên vật tư</th>
                                                        <th class="text-center align-middle" style="width: 10%;">Đvt</th>
                                                        <th class="text-center align-middle" style="width: 10%;">Số lượng yêu cầu</th>
                                                        <th class="text-center align-middle" style="width: 10%;">Số lượng thực xuất</th>
                                                        <th class="text-center align-middle">Ghi chú</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($supplies as $index => $supply)
                                                        <tr>
                                                            <td class="text-center align-middle">{{ $index + 1 }}</td>
                                                            <td class="text-center align-middle">{{ $supply['maso'] }}</td>
                                                            <td class="text-center align-middle">{{ $supply['tenvattu'] }}</td>
                                                            <td class="text-center align-middle">{{ $supply['donvitinh'] }}</td>
                                                            <td class="text-center align-middle soluongyeucau">-</td>
                                                            <td class="text-center align-middle soluongthucnhan" data-soluongconlai="{{ $supply['soluong_conlai'] }}">-</td> {{-- Giả sử số lượng thực xuất là số lượng còn lại --}}
                                                            <td class="text-center align-middle">{{ $supply['note'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            {{-- Ngày tháng năm --}}
                                <div class="row justify-content-end mt-4">
                                    <div class="col-auto">
                                        Ngày {{$ngay}} tháng {{$thang}} năm {{$nam}}
                                    </div>
                                </div>
                            {{-- Phần ký nhận --}}
                                <div class="row text-center mt-4">
                                    <div class="col"><div class="bold">QĐ Kho vật tư</div></div>
                                    <div class="col"><div class="bold">Người Nhận</div></div>
                                    <div class="col"><div class="bold">Người cấp</div></div>
                                    <div class="col"><div class="bold">Người lập</div></div>
                                </div>

                                <div class="row text-center mt-4">
                                    <div class="col">
                                        <div class="signature-space"></div>
                                        <div class="bold">Phan Quốc Hoàng</div>
                                    </div>
                                    <div class="col">
                                        <div class="signature-space"></div>
                                        <div class="bold">{{$nguoiNhan}}</div>
                                    </div>
                                    <div class="col">
                                        <div class="signature-space"></div>
                                        <div class="bold">{{$nguoiCap}}</div>
                                    </div>
                                    <div class="col">
                                        <div class="signature-space"></div>
                                        <div class="bold">{{$nguoiLap}}</div>
                                    </div>
                                </div>
                            {{-- BUTTON --}}
                                <div class="d-flex justify-content-center mt-4 loaibo">
                                    <button type="button" class="btn btn-primary me-2" id="hoanThanh">Hoàn thành</button>
                                    <button type="button" class="btn btn-secondary" id="capNhatSoLuong">Cập nhật</button>
                                    <button id="inPDF" class="btn btn-primary" style="display: none;">
                                        <i class="bi bi-printer"></i> In
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="troveButton">Trở về</button>
                </div>
              </div>
            </div>
          </div>
    </main><!-- End #main -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.3/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

    <script src="{{ asset('assets/js/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/quagga.min.js') }}"></script>
    <script src="{{asset('assets/js/select2.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            $("#troveButton, #Close").click(function() {
                var projectId = '{{$projectId}}';
                var routeURL = "{{ route('listExportWarehouse', ['project' => ':projectId', 'module' => 'Xuất kho']) }}";
                routeURL = routeURL.replace(':projectId', projectId);
                window.location.href = routeURL;
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#capNhatSoLuong").click(function() {
                var $btn = $(this);

                if ($btn.html().includes("Cập nhật")) {
                    // Chuyển nút thành "Lưu"
                    $btn.html('<i class="fas fa-save"></i> Lưu');

                    // Chuyển đổi nội dung các ô thành input
                    $(".danhmucvattuchitiet tbody tr").each(function() {
                        var $this = $(this);

                        // Số lượng yêu cầu
                        var soluongYeuCau = $this.find('.soluongyeucau').text().trim();
                        soluongYeuCau = soluongYeuCau === '-' ? '' : soluongYeuCau;
                        $this.find('.soluongyeucau').html('<input type="number" min="1" class="form-control" value="' + soluongYeuCau + '" placeholder="Nhập SL yêu cầu"/>');

                        // Số lượng thực xuất
                        var soluongThucNhan = $this.find('.soluongthucnhan').text().trim();
                        var soluongConLai = $this.find('.soluongthucnhan').data('soluongconlai');
                        soluongThucNhan = soluongThucNhan === '-' ? '' : soluongThucNhan;
                        $this.find('.soluongthucnhan').html('<input type="number" min="0" class="form-control" value="' + soluongThucNhan + '" placeholder="Nhập SL thực xuất" max="' + soluongConLai + '"/>');
                    });
                } else {
                    var allValid = true;
                    $(".danhmucvattuchitiet tbody tr").each(function() {
                        var $this = $(this);
                        var soluongThucNhanInput = parseInt($this.find('.soluongthucnhan input').val());
                        var soluongConLai = parseInt($this.find('.soluongthucnhan').data('soluongconlai'));

                        if (soluongThucNhanInput > soluongConLai) {
                            allValid = false;
                            alert(`Không thể lớn hơn số lượng tồn kho ${soluongConLai}`);
                            return false;
                        }
                    });

                    if (allValid) {
                        $(".danhmucvattuchitiet tbody tr").each(function() {
                            var $this = $(this);

                            var soluongYeuCauInput = $this.find('.soluongyeucau input').val();
                            $this.find('.soluongyeucau').text(soluongYeuCauInput || '-');

                            var soluongThucNhanInput = $this.find('.soluongthucnhan input').val();
                            $this.find('.soluongthucnhan').text(soluongThucNhanInput || '-');
                        });
                        // Chuyển nút trở lại thành "Cập nhật"
                        $btn.html('<i class="fas fa-edit"></i> Cập nhật');
                    }
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#hoanThanh").click(function() {
                var dataIsComplete = true; // Giả sử dữ liệu ban đầu là đầy đủ

                $(".danhmucvattuchitiet tbody tr").each(function() {
                    var $this = $(this);
                    var soluongYeuCau = $this.find('.soluongyeucau').text().trim();
                    var soluongThucNhan = $this.find('.soluongthucnhan').text().trim();

                    if (soluongYeuCau === '-' || soluongThucNhan === '-' || soluongYeuCau === "" || soluongThucNhan === "") {
                        dataIsComplete = false;
                        return false; // Thoát khỏi vòng lặp nếu tìm thấy trường dữ liệu chưa được điền
                    }
                });

                if (!dataIsComplete) {
                    Swal.fire({
                        title: 'Thông tin chưa đầy đủ!',
                        text: 'Vui lòng điền đầy đủ số lượng cho hai cột số lượng yêu cầu và số lượng thực xuất trước khi hoàn thành.',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        title: 'Bạn đã tạo phiếu thành công!',
                        text: 'Bạn có muốn xuất phiếu này ra file PDF không?',
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: 'Xuất PDF',
                        cancelButtonText: 'Không'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Gọi hàm để xuất PDF
                            exportPDF();

                        }
                    });

                    $("#capNhatSoLuong, #hoanThanh").fadeOut();
                    $("#inPDF").fadeIn();
                }
            });
        });

        function exportPDF() {
            html2canvas(document.querySelector(".centered-div"), {
        onclone: function (clonedDoc) {
            // Thực hiện chỉnh sửa trên bản clone trước khi chụp
            // Ví dụ: loại bỏ box-shadow
            $(clonedDoc).find(".centered-div").css("box-shadow", "none");
        }
        }).then(function(canvas) {
                const imgData = canvas.toDataURL('image/png');

                // Khởi tạo jsPDF sử dụng cú pháp mới
                const pdf = new jspdf.jsPDF({
                    orientation: 'p',
                    unit: 'mm',
                    format: 'a4'
                });

                const imgProps = pdf.getImageProperties(imgData);
                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                pdf.save('phieu-xuat-kho.pdf');
            });
        }


    </script>
    <script>
        $(document).ready(function() {
            $("#inPDF").click(function() {
                var contentToPrint = $("#contentToPrint").clone();
                contentToPrint.find('.loaibo').remove();
                var printContents = contentToPrint.html();
                var originalContents = $("body").html();
                $("body").html(printContents);
                window.print();
                $("body").html(originalContents);
            });
        });
    </script>

</body>
</html>
