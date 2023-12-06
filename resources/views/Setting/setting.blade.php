@extends('Layout.app')
@section('title')
    Cài đặt hệ thống
@endsection

@section('content')
    <div class="pagetitle">
        <h1>Cài đặt hệ thống</h1>
        <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Trang chủ</a></li>
            <li class="breadcrumb-item active">Cài đặt hệ thống</li>
        </ol>
        </nav>
    </div>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Cài đặt </h5>
            <ul class="nav nav-tabs d-flex" id="myTabjustified" role="tablist">
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 active" id="department-tab" data-bs-toggle="tab"
                        data-bs-target="#department-justified" type="button" role="tab" aria-controls="department"
                        aria-selected="true">Quản Lý Phòng Ban</button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100" id="user-tab" data-bs-toggle="tab" data-bs-target="#user-justified"
                        type="button" role="tab" aria-controls="user" aria-selected="false">Quản Lý Người Dùng</button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100" id="category-tab" data-bs-toggle="tab"
                        data-bs-target="#category-justified" type="button" role="tab" aria-controls="category"
                        aria-selected="false">Quản Lý Nhà Cung Cấp</button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content pt-2" id="myTabjustifiedContent">
                <div class="tab-pane fade show active" id="department-justified" role="tabpanel"
                    aria-labelledby="department-tab">
                    <table class="table table-borderless table-bordered departments mt-3">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center">STT</th>
                                <th scope="col" style="text-align: center">Tên phòng ban</th>
                                <th scope="col" style="text-align: center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                </div>
                <div class="tab-pane fade" id="user-justified" role="tabpanel" aria-labelledby="user-tab">
                    @if (($user->position->id == 11))
                    <button type="button" class="btn btn-outline-primary add-user" data-bs-target="#add-User" data-bs-toggle="modal">+ Thêm người dùng</button>
                    @endif
                    <table class="table table-borderless table-bordered users mt-3">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center">STT</th>
                                <th scope="col" style="text-align: center">Họ và tên</th>
                                <th scope="col" style="text-align: center">Mã số nhân viên</th>
                                <th scope="col" style="text-align: center">Mail</th>
                                <th scope="col" style="text-align: center">Phòng ban</th>
                                <th scope="col" style="text-align: center">Chức vụ</th>
                                <th scope="col" style="text-align: center">Chức năng</th>
                                <th scope="col" style="text-align: center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="category-justified" role="tabpanel" aria-labelledby="category-tab">
                    @if (($user->position->id == 11))
                        <button type="button" class="btn btn-outline-primary add-category" data-bs-target="#add-provider" data-bs-toggle="modal">+ Thêm nhà cung cấp</button>
                    @endif
                    <table class="table table-borderless table-bordered mt-2" id="providers" >
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center">STT</th>
                                <th scope="col" style="text-align: center">Tên nhà cung cấp</th>
                                <th scope="col" style="text-align: center">Mô tả</th>
                                <th scope="col" style="text-align: center">Chủng loại</th>
                                <th scope="col" style="text-align: center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<!-- SỬA THÔNG TIN NHÂN SỰ -->
    <div class="modal fade" id="Edit-User" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">SỬA THÔNG TIN NHÂN SỰ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <label for="inputText" class="col-sm-2 col-form-label">Họ và tên:</label>
                    <div class="col-sm-10">
                      <input type="text" id="edit-name" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="inputText" class="col-sm-2 col-form-label">MSNV:</label>
                    <div class="col-sm-10">
                      <input id="edit-msnv" type="text" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="inputText" class="col-sm-2 col-form-label">Mail:</label>
                    <div class="col-sm-10">
                      <input id="edit-email" type="text" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Phòng ban</label>
                    <div class="col-sm-10">
                      <select id="edit-department" class="form-select" aria-label="Default select example">
                        @foreach ($departments as $value)
                            <option value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach
                      </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Chức vụ</label>
                    <div class="col-sm-10">
                      <select id="edit-position" class="form-select" aria-label="Default select example">
                            @foreach ($position as $value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                      </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Chức năng</label>
                    <div class="col-sm-10">
                      <select id="edit-appFunction" class="form-select" aria-label="Default select example">
                            @foreach ($AppFunction as $value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                      </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary submit-edit-user">Sửa</button>
            </div>
            </div>
        </div>
    </div>
<!-- THÊM NHÂN SỰ -->
    <div class="modal fade" id="add-User" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">THÊM NHÂN SỰ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <label for="inputText" class="col-sm-2 col-form-label">Họ và tên:</label>
                    <div class="col-sm-10">
                      <input type="text" id="add-name" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="inputText" class="col-sm-2 col-form-label">MSNV:</label>
                    <div class="col-sm-10">
                      <input id="add-msnv" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="inputText" class="col-sm-2 col-form-label">Mail:</label>
                    <div class="col-sm-10">
                      <input id="add-email" type="text" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Phòng ban</label>
                    <div class="col-sm-10">
                      <select id="add-department" class="form-select" aria-label="Default select example">
                        @foreach ($departments as $value)
                            <option value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach
                      </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Chức vụ</label>
                    <div class="col-sm-10">
                      <select id="add-position" class="form-select" aria-label="Default select example">
                            @foreach ($position as $value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                      </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Chức năng</label>
                    <div class="col-sm-10">
                      <select id="add-appFunction" class="form-select" aria-label="Default select example">
                            @foreach ($AppFunction as $value)
                                <option value="{{$value->id}}">{{$value->name}}</option>
                            @endforeach
                      </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary submit-add-user">Thêm</button>
            </div>
            </div>
        </div>
    </div>
<!-- THÊM NHÀ CUNG CẤP -->
    <div class="modal fade" id="add-provider" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">THÊM NHÀ CUNG CẤP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <label for="inputText" class="col-sm-2 col-form-label">Tên NCC</label>
                    <div class="col-sm-10">
                    <input type="text" id="add-providerDetail" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="inputText" class="col-sm-2 col-form-label">Mô tả</label>
                    <div class="col-sm-10">
                    <input type="text" id="add-describe" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Chủng loại</label>
                    <div class="col-sm-10">
                    <select  class="form-select add-provider1" aria-label="Default select example">
                        @foreach ($providers as $value)
                            <option value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach
                    </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary submit-add-provider">Thêm</button>
            </div>
            </div>
        </div>
    </div>
<!-- SỬA NHÀ CUNG CẤP -->
    <div class="modal fade" id="edit-provider" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">SỬA NHÀ CUNG CẤP</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-provider-id">
                <div class="row mb-3">
                    <label for="inputText" class="col-sm-2 col-form-label">Tên NCC</label>
                    <div class="col-sm-10">
                    <input type="text" id="edit-providerDetail" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="inputText" class="col-sm-2 col-form-label">Mô tả</label>
                    <div class="col-sm-10">
                    <input type="text" id="edit-describe" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Chủng loại</label>
                    <div class="col-sm-10">
                    <select id="" class="form-select edit-provider" aria-label="Default select example">
                        @foreach ($providers as $value)
                            <option value="{{$value->id}}">{{$value->name}}</option>
                        @endforeach
                    </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary submit-edit-provider">Sửa</button>
            </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
<script>
//------------------ HIỂN THỊ DANH SÁCH PHÒNG BAN -------------------//
    function getDepartments() {
        return fetch("{{route('listDepartment')}}")
            .then(response => response.json())
            .then(data => data.departments);
    }
    $(document).ready(function() {
        $.ajax({
            url: "{{route('listDepartment')}}",
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                let rows = '';
                $.each(data, function(index, department) {
                    rows += `
                        <tr>
                            <td style="text-align: center">${index + 1}</td>
                            <td style="text-align: center">${department.name}</td>
                            <td style="text-align: center">
                                <button class="btn btn-sm btn-primary edit-btn" data-name="${department.name}" data-id="${department.id}">Sửa</button>
                            </td>
                        </tr>
                    `;
                });
                $('.departments tbody').html(rows);
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
//------------------ SỬA PHÒNG BAN ----------------------------------//
    $(document).on('click', '.edit-btn', function() {
        let departmentName = $(this).data('name');
        let departmentId = $(this).data('id');
        let btn = $(this);

        Swal.fire({
            title: 'Chỉnh sửa phòng ban',
            input: 'text',
            inputValue: departmentName,
            showCancelButton: true,
            confirmButtonText: 'Lưu',
            cancelButtonText: 'Hủy',
            showLoaderOnConfirm: true,
            preConfirm: (newName) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: "{{route('editDepartment')}}", // URL của bạn
                        method: 'POST',
                        data: {
                            id: departmentId,
                            name: newName,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                resolve();
                            } else {
                                reject('Lỗi cập nhật');
                            }
                        },
                        error: function(error) {
                            reject('Lỗi cập nhật');
                        }
                    });
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                // Cập nhật giao diện
                btn.closest('tr').find('td:nth-child(2)').text(result.value);

                // Hiển thị toast thông báo
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Cập nhật thành công',
                    showConfirmButton: false,
                    timer: 1500,
                    toast: true
                });
            }
        });
    });
//------------------ HIỂN THỊ DANH SÁCH NGƯỜI DÙNG ------------------//
    $(document).ready(function() {
        $.ajax({
            url: "{{route('listUser')}}",
            method: 'GET',
            dataType: 'json',
            success: function(data) {

                let rows = '';
                $.each(data, function(index, user) {
                    rows += `
                        <tr>
                            <td style="text-align: center">${index + 1}</td>
                            <td style="text-align: center">${user.name}</td>
                            <td style="text-align: center">${user.msnv}</td>
                            <td style="text-align: center">${user.email}</td>
                            <td style="text-align: center">${user.department ? user.department.name : 'Không có phòng ban'}</td>
                            <td style="text-align: center">${user.position ? user.position.name : 'Không có chức vụ'}</td>
                            <td style="text-align: center">${user.app_function ? user.app_function.name : 'Không có chức năng'}</td>
                            <td style="text-align: center">
                                @if (($user->position->id == 11))
                                    <button class="btn btn-sm btn-primary edit-user" data-bs-target="#Edit-User" data-bs-toggle="modal">Sửa</button>
                                    <button class="btn btn-sm btn-danger delete-user" data-id="${user.id}">Delete</button>
                                @endif
                            </td>
                        </tr>
                    `;
                });
                $('.users tbody').html(rows);
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
//------------------ SỬA NGƯỜI DÙNG ---------------------------------//
    $(document).on('click', '.edit-user', function() {
        let userRow = $(this).closest('tr');
        let name = userRow.find('td:eq(1)').text();
        let msnv = userRow.find('td:eq(2)').text();
        let email = userRow.find('td:eq(3)').text();

        // Đặt giá trị cho các trường input trong modal
        $('#edit-name').val(name);
        $('#edit-msnv').val(msnv);
        $('#edit-email').val(email);
    });

    $(document).on('click', '.submit-edit-user', function() {
        let name = $('#edit-name').val();
        let msnv = $('#edit-msnv').val();
        let email = $('#edit-email').val();
        let departmentId = $('#edit-department').val();
        let positionId = $('#edit-position').val();
        let appFunctionId = $('#edit-appFunction').val();
        // console.log(name,msnv,email,departmentId,positionId,appFunctionId)
        $.ajax({
            url: "{{route('editUser')}}",

            method: 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                "name": name,
                "msnv": msnv,
                "email": email,
                "department_id": departmentId,
                "position_id": positionId,
                "app_function_id": appFunctionId
            },
            success: function(response) {
                // Xử lý khi cập nhật thành công
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: 'Cập nhật thông tin người dùng thành công.',
                    showConfirmButton: true,
                    timer: 2000
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    } else {
                        // Tải lại trang sau khi hết thời gian timer
                        location.reload();
                    }
                });
            },
            error: function(error) {
                console.log(error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Có lỗi xảy ra khi cập nhật thông tin.',
                    showConfirmButton: true
                });
            }
        });
    });
//------------------ XÓA NGƯỜI DÙNG ---------------------------------//
    $(document).on('click', '.delete-user', function() {
        let userId = $(this).data('id');

        // Hiển thị SweetAlert2 để xác nhận việc xóa
        Swal.fire({
            title: 'Bạn có chắc chắn muốn xóa người dùng này?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                // Gửi yêu cầu AJAX để xóa người dùng
                $.ajax({
                    url: "{{route('deleteUser')}}",
                    method: 'POST',
                    data: {
                        id: userId,
                        _token: '{{ csrf_token() }}'  // Thêm token CSRF
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();  // Tải lại trang
                            });
                        } else {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(error) {
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Có lỗi xảy ra khi xóa người dùng.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
//------------------ THÊM NGƯỜI DÙNG --------------------------------//
    $(document).on('click', '.submit-add-user', function() {
        // Lấy dữ liệu từ các trường nhập
        let userName = $('#add-name').val();
        let userMsnv = $('#add-msnv').val();
        let userEmail = $('#add-email').val();
        let userDepartment = $('#add-department').val();
        let userPosition = $('#add-position').val();
        let userAppFunction = $('#add-appFunction').val();

        // Gửi yêu cầu AJAX để thêm người dùng
        $.ajax({
            url: "{{route('addUser')}}",
            method: 'POST',
            data: {
                name: userName,
                msnv: userMsnv,
                email: userEmail,
                department_id: userDepartment,
                position_id: userPosition,
                function_id: userAppFunction,
                _token: '{{ csrf_token() }}'  // Thêm token CSRF
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Hiển thị thông báo thành công và tải lại trang
                    Swal.fire({
                        title: 'Thành công!',
                        text: 'Người dùng đã được thêm thành công.',
                        icon: 'success'
                    }).then(() => {
                        location.reload();  // Tải lại trang
                    });
                } else {
                    // Hiển thị thông báo lỗi
                    Swal.fire({
                        title: 'Lỗi!',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function(error) {
                Swal.fire({
                    title: 'Lỗi!',
                    text: 'Có lỗi xảy ra khi thêm người dùng.',
                    icon: 'error'
                });
            }
        });
    });
//------------------ HIỂN THỊ NHÀ CUNG CẤP --------------------------//
    $(document).ready(function() {
        $.ajax({
            url: "{{route('listProviderDetail')}}",
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                let rows = '';
                $.each(data, function(index, detail) {
                    rows += `
                        <tr>
                            <td style="text-align: center">${index + 1}</td>
                            <td style="text-align: center">${detail.name}</td>
                            <td style="text-align: center">${detail.describe}</td>
                            <td style="text-align: center">${detail.provider.name}</td>
                            <td style="text-align: center">
                                @if (($user->position->id == 11))
                                    <button class="btn btn-sm btn-primary edit-provider-model"
                                            data-bs-target="#edit-provider"
                                            data-bs-toggle="modal"
                                            data-id="${detail.id}"
                                            data-name="${detail.name}"
                                            data-describe="${detail.describe}"
                                            data-provider-id="${detail.provider.id}">
                                        Sửa
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-provider" data-id="${detail.id}">Delete</button>
                                @endif
                            </td>
                        </tr>
                    `;
                });
                $('#providers tbody').html(rows);
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
//------------------ THÊM NHÀ CUNG CẤP ------------------------------//
    $(document).ready(function() {
        $('.submit-add-provider').on('click', function() {
            let providerDetailName = $('#add-providerDetail').val();
            let providerId = $('.add-provider1').val();

            let describe = $('#add-describe').val();
            console.log(providerId)
            $.ajax({
                url: "{{route('addProviderDetail')}}",
                method: 'POST',
                data: {
                    name: providerDetailName,
                    provider_id: providerId,
                    describe:describe,
                    _token: "{{ csrf_token() }}"
                },
                dataType: 'json',
                success: function(response) {
                    Swal.fire({
                                title: 'Thêm thành công!',
                                text: response.message,
                                icon: 'success'
                            }).then(() => {
                                location.reload();  // Tải lại trang
                            });
                },
                error: function(error) {
                    console.log(error);
                    alert('Có lỗi xảy ra khi thêm nhà cung cấp!');
                }
            });
        });
    });
//------------------ SỬA NHÀ CUNG CẤP -------------------------------//
    $(document).on('click', '.edit-provider-model', function() {
        // Lấy dữ liệu từ thuộc tính data-* của nút "Sửa"
        let providerName = $(this).data('name');
        let describe = $(this).data('describe');
        let providerId = $(this).data('provider-id');
        let id = $(this).data('id');

        // Điền dữ liệu vào modal
        $('#edit-providerDetail').val(providerName);
        $('#edit-describe').val(describe);
        $('.edit-provider').val(providerId);
        $('#edit-provider-id').val(id);  // Cập nhật giá trị của trường ẩn

        // Hiển thị modal
        $('#edit-provider').modal('show');
    });


    $('.submit-edit-provider').on('click', function() {
        let providerDetailName = $('#edit-providerDetail').val();
        let describe = $('#edit-describe').val();
        let providerId = $('.edit-provider').val();
        let id = $('#edit-provider-id').val();  // Lấy giá trị từ trường ẩn

        $.ajax({
            url: "{{route('editProviderDetail')}}",
            method: 'POST',
            data: {
                id: id,  // Truyền id lên máy chủ
                name: providerDetailName,
                describe: describe,
                provider_id: providerId,
                _token: "{{ csrf_token() }}"
            },
            dataType: 'json',
            success: function(response) {
                Swal.fire({
                    title: 'Cập nhật thành công!',
                    text: response.message,
                    icon: 'success'
                }).then(() => {
                    location.reload();  // Tải lại trang
                });
            },
            error: function(error) {
                console.log(error);
                Swal.fire({
                    title: 'Lỗi!',
                    text: 'Có lỗi xảy ra khi cập nhật nhà cung cấp!',
                    icon: 'error'
                });
            }
        });
    });

//------------------ XÓA NHÀ CUNG CẤP -------------------------------//
    $(document).on('click', '.delete-provider', function() {
        let id = $(this).data('id');
        let row = $(this).closest('tr');  // Lấy dòng dữ liệu tương ứng

        Swal.fire({
            title: 'Bạn có chắc chắn muốn xóa?',
            text: "Bạn không thể hoàn tác hành động này!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{route('deleteProviderDetail')}}",
                    method: 'POST',
                    data: {
                        id: id,
                        _token: "{{ csrf_token() }}"
                    },
                    dataType: 'json',
                    success: function(response) {
                        // Hiển thị Toast
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            title: 'Đã xóa!',
                            text: response.message,
                            icon: 'success'
                        });

                        // Làm mờ dòng dữ liệu đã xóa
                        row.fadeOut(1000, function() {
                            $(this).remove();
                        });
                    },
                    error: function(error) {
                        console.log(error);
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Có lỗi xảy ra khi xóa nhà cung cấp!',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });


</script>
@endsection
