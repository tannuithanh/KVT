@extends('Layout.app')
@section('title')
    Trang chủ | Quản lý kho
@endsection

@section('content')
    <div class="pagetitle">
        <h1>Trang chủ</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Trang chủ</a></li>
                <li class="breadcrumb-item active">Bảng điều khiển</li>
            </ol>
        </nav>
    </div>
    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-8">
                <div class="row">
                    <!-- ======= NHẬP KHO ======= -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card sales-card">

                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown" aria-expanded="false"><i
                                        class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="">
                                    <li class="dropdown-header text-start">
                                        <h6>LỌC</h6>
                                    </li>

                                    <li><a class="dropdown-item" href="#">Hôm nay</a></li>
                                    <li><a class="dropdown-item" href="#">Tháng này</a></li>
                                    <li><a class="dropdown-item" href="#">Năm nay</a></li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">Nhập kho <span>| Hôm nay</span></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                        <i class="bi bi-cart-check"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6 style="display: inline;">145</h6>
                                        <h6 style="display: inline;">sản phẩm</h6>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                    <!-- ======= XUẤT KHO ======= -->
                    <div class="col-xxl-4 col-md-6">
                        <div class="card info-card revenue-card">

                            <div class="filter">
                                <a class="icon" href="#" data-bs-toggle="dropdown"><i
                                        class="bi bi-three-dots"></i></a>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                    <li class="dropdown-header text-start">
                                        <h6>Filter</h6>
                                    </li>

                                    <li><a class="dropdown-item" href="#">Hôm nay</a></li>
                                    <li><a class="dropdown-item" href="#">Tháng này</a></li>
                                    <li><a class="dropdown-item" href="#">Năm nay</a></li>
                                </ul>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title">Xuất kho <span>| Hôm nay</span></h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                                        style="color: red; background-color: rgb(255, 219, 219)">
                                        <i class="bi bi-cart-dash"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6 style="display: inline;">12</h6>
                                        <h6 style="display: inline;">sản phẩm</h6>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- ======= TỒN KHO ======= -->
                    <div class="col-xxl-4 col-xl-12">
                        <div class="card info-card revenue-card">

                            <div class="card-body">
                                <h5 class="card-title">Tồn kho</h5>

                                <div class="d-flex align-items-center">
                                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center"
                                        style="color: rgb(255, 136, 0); background-color: rgb(252, 234, 219)">
                                        <i class="bi bi-card-checklist"></i>
                                    </div>
                                    <div class="ps-3">
                                        <h6 style="display: inline;">12</h6>
                                        <h6 style="display: inline;">sản phẩm</h6>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card">
                      <div class="card-body">
                        <h5 class="card-title">Bar Chart</h5>
                      </div>
                    </div>
                  </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                      <h5 class="card-title">Thông tin chi tiết</h5>
                        <div class="activity-item d-flex justify-content-between">
                            <div class="activite-label">Xe Bus</div>
                            <div>Số dự án: 5</div>
                            <div>Tổng số vật tư: 100</div>
                        </div>

                        <div class="activity-item d-flex justify-content-between">
                            <div class="activite-label">Xe Tải</div>
                            <div>Số dự án: 3</div>
                            <div>Tổng số vật tư: 50</div>
                        </div>
                        <div class="activity-item d-flex">
                            <div class="activite-label">Xe Du Lịch:</div>
                        </div>
                        <div class="activity-item d-flex">
                            <div class="activite-label">Xe Royal:</div>
                        </div>
                        <div class="activity-item d-flex">
                            <div class="activite-label">Xe Hai Bánh:</div>
                        </div>
                        <div class="activity-item d-flex">
                            <div class="activite-label">Xe Nông Nghiệp:</div>
                        </div>
                      </div>

                    </div>
                  </div>
            </div>
        </div>

    </section>
@endsection
