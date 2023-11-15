@extends('Layout.app')

@section('title')
    Thông tin cá nhân
@endsection

@section('content')
    <div class="pagetitle">
        <h1>Thông tin cá nhân</h1>
        <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Trang chủ</a></li>
            <li class="breadcrumb-item active">Thông tin cá nhân</li>
        </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
        <div class="row">
        <div class="col-xl-4">
            <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
                <img src="{{asset('assets/img/User_icon_2.svg.png')}}" alt="Profile" class="rounded-circle">
                <h2>{{$user->name}}</h2>
                <h3>{{$user->position->name ?? ''}}</h3>
            </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card">
            <div class="card-body pt-3">
                <!-- Bordered Tabs -->
                <ul class="nav nav-tabs nav-tabs-bordered">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Thông tin cá
                    nhân</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Đổi mật
                    khẩu</button>
                </li>
                </ul>
                <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">

                    <h5 class="card-title">Thông tin chi tiết</h5>
                    <div class="row">
                    <div class="col-lg-3 col-md-4 label ">Họ và tên:</div>
                    <div class="col-lg-9 col-md-8">{{$user->name}}</div>
                    </div>
                    <div class="row">
                    <div class="col-lg-3 col-md-4 label">Phòng/Ban:</div>
                    <div class="col-lg-9 col-md-8">{{$user->department->name ?? 'Không có phòng ban'}}</div>
                    </div>
                    <div class="row">
                    <div class="col-lg-3 col-md-4 label">chức vụ:</div>
                    <div class="col-lg-9 col-md-8">{{$user->position->name ?? ''}}</div>
                    </div>
                    <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email:</div>
                    <div class="col-lg-9 col-md-8">{{$user->email}}</div>
                    </div>
                </div>

                <div class="tab-pane fade pt-3" id="profile-change-password">
                    <form method="POST">
                        @csrf
                        <div class="row mb-3">
                          <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Nhập mật khẩu cũ:</label>
                          <div class="col-md-8 col-lg-9">
                            <input name="password" type="password" class="form-control" id="currentPassword">
                          </div>
                        </div>
      
                        <div class="row mb-3">
                          <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">Mật khẩu mới:</label>
                          <div class="col-md-8 col-lg-9">
                            <input name="newpassword" type="password" class="form-control" id="newPassword">
                          </div>
                        </div>
      
                        <div class="row mb-3">
                          <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Nhập lại mật khẩu mới:</label>
                          <div class="col-md-8 col-lg-9">
                            <input name="renewpassword" type="password" class="form-control" id="renewPassword">
                          </div>
                        </div>
      
                        <div class="text-center">
                          <button type="submit" class="btn btn-primary">Đổi mật khẩu</button>
                        </div>
                    </form>

                </div>

                </div><!-- End Bordered Tabs -->

            </div>
            </div>

        </div>
        </div>
    </section>
@endsection
@section('script')
<script>
   $(document).ready(function() {
      $("form").on("submit", function(event) {
          event.preventDefault();

          Swal.fire({
              title: 'Bạn có chắc chắn?',
              text: "Bạn có muốn thay đổi mật khẩu không?",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Có, thay đổi!',
              cancelButtonText: 'Hủy'
          }).then((result) => {
              if (result.isConfirmed) {
                  $.ajax({
                      type: "POST",
                      url: '{{ route("changePassword") }}',
                      data: {
                          password: $("#currentPassword").val(),
                          newpassword: $("#newPassword").val(),
                          renewpassword: $("#renewPassword").val(),
                          _token: '{{ csrf_token() }}'
                      },
                      success: function(response) {
                          if (response.error) {
                              Swal.fire('Lỗi!', response.error, 'error');
                          } else {
                              Swal.fire('Thành công!', response.success, 'success');
                          }
                      },
                      error: function() {
                          Swal.fire('Lỗi!', 'Lỗi xảy ra. Vui lòng thử lại sau.', 'error');
                      }
                  });
              }
          });
      });
  });
</script>
@endsection