@extends('Layout.app')
@section('style')
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

                              </tbody>
                            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
@endsection
