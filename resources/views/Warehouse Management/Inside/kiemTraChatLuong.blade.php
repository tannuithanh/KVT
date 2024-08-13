@extends('Layout.app')

@section('style')
<style>
    .table th, .table td {
        text-align: center;
        vertical-align: middle;
    }
    .info-card {
        text-align: center;
        padding: 20px 0;
    }

    .card-custom {
        margin-bottom: 20px;
        border: 1px solid #05438a;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    .card-custom:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        cursor: pointer;
    }
    .card-custom .card-header {
        background-color: #05438a;
        color: white;
        font-weight: bold;
        text-align: center;
        border-bottom: 1px solid #05438a;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        padding: 10px;
    }
    .card-custom .card-body {
        display: flex;
        justify-content: space-between;
        padding: 15px;
        font-size: 14px;
    }
    .card-custom .card-body span {
        flex: 1;
    }
    .card-custom .card-body span:last-child {
        text-align: right;
        font-weight: bold;
        color: #05438a;
    }
</style>
@endsection

@section('title')
    Kiểm tra chất lượng | Quản lý kho
@endsection

@section('content')
<div class="pagetitle">
    <h1>Kiểm tra chất lượng</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashBoard')}}">Trang chủ</a></li>
            <li class="breadcrumb-item">Kiểm tra chất lượng</li>
        </ol>
    </nav>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="mt-2" style="font-size: 18px;font-weight: 600;color: #012970;">Danh sách đơn hàng cần kiểm tra chất lượng</h5>
                    <div class="row mt-3">
                        @foreach ($orderQuantities as $orderId => $order)
                            <div class="col-md-3">
                                <div class="card card-custom" data-order-id="{{ $orderId }}">
                                    <div class="card-header">
                                        {{ $order['order_number'] }}
                                    </div>
                                    <div class="card-body">
                                        <span>Số lượng cần kiểm tra:</span>
                                        <span>{{ $order['total_quantity'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
        <script>
            $(document).ready(function() {
                $('.card-custom').on('click', function() {
                    var orderId = $(this).data('order-id');

                    $.ajax({
                        url: "{{ route('vatTuKiemTra') }}",
                        type: "POST",
                        data: {
                            order_id: orderId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            console.log(response);

                            var masos = response.map(function(item) {
                                return item.supply.maso; // Giả sử mỗi item trả về có một property 'supply' với 'maso'
                            });

                            // Encode the masos array as a JSON string and then URI encode it
                            var encodedMasos = encodeURIComponent(JSON.stringify(masos));

                            // Redirect to the route with query parameter
                            window.location.href = "{{ route('kiemTraCLBarcode') }}" + "?masos=" + encodedMasos;
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                });
            });
            </script>

@endsection

