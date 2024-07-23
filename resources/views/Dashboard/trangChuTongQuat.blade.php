
@extends('Layout.app')

@section('style')
@endsection

@section('title')
    Trang chủ | Quản lý kho
@endsection

@section('content')
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-body" style="background-image: url('{{ asset('assets/img/BG1.jpg') }}'); background-size: contain; background-position: center; min-height: 500px; height: calc(90vh - 100px);">
                    <!-- Thẻ thông tin tổng quan -->
                </div>
            </div>
        </div>
    </div>
</section>
        {{-- <!-- Modal kiểm tra chất lượng -->
        <div class="modal fade" id="qualityCheckModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Đơn hàng cần kiểm tra chất lượng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="list-group">
                            @foreach ($ordersNeedQualityCheck as $order)
                                <li class="list-group-item">Đơn hàng số: {{ $order->sodonhang }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <a href="{{route('checkQuality')}}" class="btn btn-primary" id="checkQualityBtn">Kiểm tra</a>
                    </div>
                </div>
            </div>
        </div> --}}

@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    @if ($showPopup && !$ordersNeedQualityCheck->isEmpty())
        window.onload = function() {
            $('#qualityCheckModal').modal('show');
        }
    @endif
</script>
@endsection
