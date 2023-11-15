<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Đăng nhập - Quản Lý kho</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

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
</head>
<body>
  <main style="background-image: url('assets/img/BG.JPG');
  background-size: cover;
  background-position: center;
  width: 100%;
  height: 100%;">
   <div class="background-overlay" style="position: fixed;"></div>
    <div class="container">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="card mb-3">
                <div class="card-body" >
                  <div class="pt-4 pb-2" style="text-align: center">
                    <a href="index.html" class="logo d-flex flex-column align-items-center w-auto">
                      <img src="{{asset('assets/img/logo.png')}}" alt="" style="max-height: 46px;">
                      <span class="d-none d-lg-block"></span>
                    </a>
                    <h5 class="card-title text-center pb-0 fs-4">ỨNG DỤNG QUẢN LÝ KHO</h5>
                    <img src="{{asset('assets/img/4080.jpg')}}" alt="" style="width:80%;" class="auth-logo logo-dark mx-auto">
                    <p class="text-center "> Vui lòng đăng nhập!</p>
                  </div>

                  <form class="row g-3 needs-validation" method="post">
                    @csrf
                    <div class="col-12">
                      <label for="yourUsername" class="form-label">Tài khoản:</label>
                      <div class="input-group has-validation">
                        <span class="input-group-text" id="inputGroupPrepend">MSNV</span>
                        <input type="number" name="msnv" class="form-control" id="yourUsername" required>
                        <div class="invalid-feedback">Vui lòng điền mã số nhân viên.</div>
                      </div>
                    </div>

                    <div class="col-12">
                      <label for="yourPassword" class="form-label">Mật khẩu:</label>
                      <input type="password" name="password" class="form-control" id="yourPassword" required>
                      <div class="invalid-feedback">Vui lòng điền mật khẩu!</div>
                    </div>

                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Nhớ tài khoản </label>
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" type="submit">Đăng nhập</button>
                    </div>
                    <div class="col-12 text-center" >
                      <p class="small mb-0"> <a href="">Quên mật khẩu</a></p>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i
      class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="{{asset('assets/vendor/apexcharts/apexcharts.min.js')}}"></script>
  <script src="{{asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{asset('assets/vendor/chart.js/chart.umd.js')}}"></script>
  <script src="{{asset('assets/vendor/echarts/echarts.min.js')}}"></script>
  <script src="{{asset('assets/vendor/quill/quill.min.js')}}"></script>
  <script src="{{asset('assets/vendor/simple-datatables/simple-datatables.js')}}"></script>
  <script src="{{asset('assets/vendor/tinymce/tinymce.min.js')}}"></script>
  <script src="{{asset('assets/vendor/php-email-form/validate.js')}}"></script>
  <script src="{{asset('assets/js/main.js')}}"></script>

</body>

</html>