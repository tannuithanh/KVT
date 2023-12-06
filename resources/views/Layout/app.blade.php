<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>@yield('title', '')</title>
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
  <link href="{{asset('assets/css/TH.css')}}" rel="stylesheet">
  <link href="{{asset('assets/css/loadingPage.css')}}" rel="stylesheet">
  <link href="{{ asset('assets/css/sweetalert2.min.css') }}" rel="stylesheet">
  @yield('style')
  <style>
        body {
                font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
            }
            /* Tùy chỉnh chiều rộng của thanh kéo */
            ::-webkit-scrollbar {
                width: 12px;  /* Tùy chỉnh chiều rộng */
            }

            /* Tùy chỉnh màu nền của track */
            ::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            /* Tùy chỉnh màu của handle */
            ::-webkit-scrollbar-thumb {
                background: #888;
            }

            /* Khi rê chuột lên thanh kéo */
            ::-webkit-scrollbar-thumb:hover {
                background: #555;
            }


  </style>
</head>
 <!-- ======= Loading Page ======= -->
<div class='container1'>
    <div class='loader'>
      <div class='loader--dot'></div>
      <div class='loader--dot'></div>
      <div class='loader--dot'></div>
      <div class='loader--dot'></div>
      <div class='loader--dot'></div>
      <div class='loader--dot'></div>
      <div class='loader--text'></div>
    </div>
</div>
<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
        <a class="logo d-flex align-items-center">
            <img src="{{asset('assets/img/abc.png')}}" alt="">
            <div style="text-align: center;">
                <span class="d-none d-lg-block">
                    QUẢN LÝ VẬT TƯ
                </span>
                <span class="d-none d-lg-block">
                    R&D CENTER
                </span>
            </div>
        </a>
        <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0"  data-bs-toggle="dropdown">
            <img src="{{asset('assets/img/User_icon_2.svg.png')}}" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2">Hi, {{$user->name}}</span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6>{{$user->name}}</h6>
              <span>{{$user->position->name ?? ''}}</span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{route('profile')}}">
                <i class="bi bi-person"></i>
                <span>Thông tin nhân sự</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{route('setting')}}">
                <i class="bi bi-gear"></i>
                <span>Cài đặt hệ thống</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a style="color: red" class="dropdown-item d-flex align-items-center" href="{{route('logout')}}">
                <i class="bi bi-box-arrow-right"></i>
                <span>Đăng xuất</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  @php
    $currentRoute = Route::currentRouteName();
    $isActiveWarehouse = Illuminate\Support\Str::startsWith($currentRoute, 'Warehouse-Management') || in_array($currentRoute, ['listBrand', 'listProject','listWarehouse']);
    $isActiveDashboard = $currentRoute == 'dashboard';
  @endphp

  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link {{ $isActiveDashboard ? '' : 'collapsed' }}" href="{{route('dashboard')}}">
            <i class="bi bi-grid"></i>
            <span>Trang chủ</span>
        </a>
      </li>

      <li class="nav-item">
          <a class="nav-link {{ $isActiveWarehouse ? '' : 'collapsed' }}" data-bs-target="#tables-nav" data-bs-toggle="collapse" href="#">
              <i class="bi bi-layout-text-window-reverse"></i><span>Quản lý kho</span><i class="bi bi-chevron-down ms-auto"></i>
          </a>
          <ul id="tables-nav" class="nav-content collapse {{ $isActiveWarehouse ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
              <li>
                <a href="{{route('listBrand')}}" class="{{ in_array($currentRoute, ['listBrand', 'listProject','listWarehouse']) ? 'active' : '' }}">
                      <i class="bi bi-circle"></i><span>Quản lý kế hoạch</span>
                  </a>
              </li>
          </ul>
        @if ($user->appFunction->id == 1)
            <ul id="tables-nav" class="nav-content collapse {{ $isActiveWarehouse ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="">
                            <i class="bi bi-circle"></i><span>Nhập kho</span>
                        </a>
                </li>
            </ul>
        @endif
        @if ($user->appFunction->id == 2)
            <ul id="tables-nav" class="nav-content collapse {{ $isActiveWarehouse ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                <a href="">
                        <i class="bi bi-circle"></i><span>Xuất kho</span>
                    </a>
                </li>
            </ul>
        @endif
      </li>

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    @yield('content')

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; phát triển bởi <strong><span>Phòng Quản Trị Dữ Liệu.</span></strong>
    </div>
    <div class="credits">
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{ asset('assets/js/jquery.min.js')}}"></script>
  <script src="{{ asset('assets/js/sweetalert2.all.min.js')}}"></script>
  <script src="{{asset('assets/vendor/apexcharts/apexcharts.min.js')}}"></script>
  <script src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('assets/vendor/chart.js/chart.umd.js')}}"></script>
  <script src="{{asset('assets/vendor/echarts/echarts.min.js')}}"></script>
  <script src="{{asset('assets/vendor/quill/quill.min.js')}}"></script>
  <script src="{{asset('assets/vendor/simple-datatables/simple-datatables.js')}}"></script>
  <script src="{{asset('assets/vendor/tinymce/tinymce.min.js')}}"></script>
  <script src="{{asset('assets/vendor/php-email-form/validate.js')}}"></script>
  <script src="{{asset('assets/js/main.js')}}"></script>
  @yield('script')
  <script>
    $(document).ready(function() {
        $('.container1').hide(); // Ẩn loader khi trang được tải xong

        $(document).on('click', 'a', function(e) {
            var href = $(this).attr('href');
            if(href && href !== "#" && href !== "javascript:void(0)") {
            $('.container1').show(); // Hiển thị loader chỉ khi thẻ a có href cụ thể
            }
        });

        $(window).on('load', function() {
            $('.container1').hide(); // Ẩn loader khi trang mới được tải xong
        });
    });

  </script>
</body>

</html>
