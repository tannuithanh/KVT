@extends('Layout.app')

@section('style')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/Dashboard/trangChuTongQuat.css') }}">
@endsection

@section('title')
    Trang chủ | Quản lý kho
@endsection

@section('content')
<section class="section">
    <div class="row">
        <!-- Thẻ card chứa thống kê vật tư -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">Thống kê vật tư</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="stat-box total-project-supplies">
                                <span class="icon"><i class="fas fa-boxes"></i></span>
                                <h3 id="totalMaterials">{{ $countCatalogPending }}</h3>
                                <p>Tổng danh mục vật tư đang chờ đặt hàng</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="stat-box total-supplies-received">
                                <span class="icon"><i class="fas fa-warehouse"></i></span>
                                <h3 id="materialsReceived">{{ $receivedSupplies }}</h3>
                                <p>Tổng loại vật tư đang lưu kho</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="stat-box total-supplies-pending">
                                <span class="icon"><i class="fas fa-hourglass-half"></i></span>
                                <h3 id="materialsPending">{{ $pendingSupplies }}</h3>
                                <p>Tổng loại vật tư chưa nhận</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <!-- Thẻ card chứa thống kê vật tư -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">Bảng danh mục vật tư chưa tạo đơn hàng</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Tên danh mục vật tư</th>
                                    <th>Nhà cung cấp</th>
                                    <th>Tổng số lượng vật tư</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($catalogsCanCreateOrders as $index => $catalog)
                                    <tr>
                                        <td style="text-align: center">{{ $index + 1 }}</td>
                                        <td style="text-align: center">{{ $catalog->name }}</td>
                                        <td style="text-align: center">{{ $catalog->provider_info->name }}</td>
                                        <td style="text-align: center">{{ $catalog->supplies->count() }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
{{-- <!-- Tương tự cho danh mục -->
<div class="col-lg-12">
    <div class="card mb-4">
        <div class="card-header">Thống kê danh mục</div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="stat-box total-categories">
                        <span class="icon"><i class="fas fa-list"></i></span>
                        <h3 id="totalCategories">{{ $totalCatalogs }}</h3>
                        <p>Tổng số danh mục</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="stat-box categories-completed">
                        <span class="icon"><i class="fas fa-check-circle"></i></span>
                        <h3 id="categoriesCompleted">{{ $completedCatalogs }}</h3>
                        <p>Tổng danh mục hoàn thành</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="stat-box categories-in-progress">
                        <span class="icon"><i class="fas fa-spinner"></i></span>
                        <h3 id="categoriesInProgress">{{ $pendingCatalogs }}</h3>
                        <p>Tổng danh mục đang thực hiện</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

@endsection

@section('script')
<script src="{{ mix('js/Dashboard/trangChuTongQuat.js') }}"></script>
@endsection
