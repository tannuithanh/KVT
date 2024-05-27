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
</style>
@endsection

@section('title')
    Trang chủ | Quản lý kho
@endsection

@section('content')
<div class="pagetitle">
    <h1>Trang chủ</h1>
</div>
<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
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
</div>
@endsection

@section('script')
<script>
    @if ($showPopup && !$ordersNeedQualityCheck->isEmpty())
        window.onload = function() {
            $('#qualityCheckModal').modal('show');
        }
    @endif
</script>
@endsection
