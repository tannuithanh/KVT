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
              font-family: Arial, sans-serif;
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
  <header id="header" class="header fixed-top d-flex align-items-center" style="background-color: #05438a;color: white !important; font-family:Arial, Helvetica, sans-serif " >

    <div class="d-flex align-items-center justify-content-between" >
        <a class="logo d-flex align-items-center bg-rounded-white" style="text-align: center;border-radius: 30px; align-items: center; background-color: white; justify-content: center; padding: 5px;">
            <div style="text-align: center;">
                <img src="{{asset('assets/img/logo.png')}}" alt="" style="max-height: 25px ">
            </div>
        </a>
        <i class="bi bi-list toggle-sidebar-btn" style="color: white"></i>
    </div><!-- End Logo -->
    <div class="header-title" style="font-size: 25px;">
        KHO VẬT TƯ R&D ÔTÔ
    </div>
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0"  data-bs-toggle="dropdown">
            <img src="{{asset('assets/img/User_icon_2.svg.png')}}" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2" style="color: white">Hi, {{$user->name}}</span>
          </a>

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
          @if (in_array($user->appFunction->id, [3, 5]))
            <ul id="tables-nav" class="nav-content collapse {{ $isActiveWarehouse ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{route('listBrand', ['module' => 'Quản lý kế hoạch'])}}" class="{{ in_array($currentRoute, ['listBrand', 'listProject','listWarehouse']) ? 'active' : '' }}">
                        <i class="bi bi-circle"></i><span>Quản lý kế hoạch</span>
                    </a>
                </li>
            </ul>
          @endif
        @if (in_array($user->appFunction->id, [1, 5]))
            <ul id="tables-nav" class="nav-content collapse {{ $isActiveWarehouse ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{route('listBrand', ['module' => 'Nhập kho'])}}" >
                            <i class="bi bi-circle"></i><span>Nhập kho</span>
                        </a>
                </li>
            </ul>
        @endif
        @if (in_array($user->appFunction->id, [2, 5]))
            <ul id="tables-nav" class="nav-content collapse {{ $isActiveWarehouse ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                <a href="{{route('listBrand', ['module' => 'Xuất kho'])}}" >
                        <i class="bi bi-circle"></i><span>Xuất kho</span>
                    </a>
                </li>
            </ul>
        @endif
      </li>

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main content">

    @yield('content')

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="left-content">
        <span>Ứng dụng quản lý vật tư linh kiện tại kho Trung Tâm R&D ÔTô</span>
    </div>
    <div class="right-content">
        <div>Bản quyền © 2023 tại Trung tâm R&D</div>
        <div><i class="fa fa-phone"></i>✆ hotline: 0886418363</div>
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
