@extends('Layout.app')

@section('title')
    Dự án
@endsection

@section('content')
    <div class="pagetitle">
        <h1>Danh sách dự án</h1>
        <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Trang chủ</a></li>
            <li class="breadcrumb-item">{{ $module }}</li>
            <li class="breadcrumb-item"><a href="{{route('listBrand', ['module' => $module])}}">Thương hiệu</a></li>
            <li class="breadcrumb-item active">Dự án</li>
        </ol>
        </nav>
    </div>

    <section class="section">
    <div class="row">
    <div class="col-lg-12">
        <div class="card">
        <div class="card-body">
            <h5 class="mt-2" style="font-size: 18px;font-weight: 600;color: #012970;">
                <span style="font-size: 18px;font-weight: 600;color: #012970;">Thương hiệu: <span style="color: red">{{ $brand->name }}</span> |
                <span style="font-size: 18px;font-weight: 600;color: #012970;">Phân khúc: <span  style="color: red">{{ $segmentName }}</span> 
            </h5>  
            @if (in_array($user->appFunction->id, [3, 5]))
                <button type="button" class="btn btn-outline-primary add-project" data-bs-toggle="modal" data-bs-target="#smallModal">+ Thêm dự án</button>
            @endif
            <table class="table table-borderless table-bordered mt-2">
            <thead>
                <tr>
                    <th style="vertical-align: middle; text-align: center;" scope="col" >STT</th>
                    <th style="vertical-align: middle; text-align: center;" scope="col">Tên dự án</th>
                    <th style="vertical-align: middle; text-align: center;" scope="col">Mô tả dự án</th>
                    <th style="vertical-align: middle; text-align: center;" scope="col">Ngày tạo</th>
                    <th style="vertical-align: middle; text-align: center;" scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @php $stt = 1; @endphp
                @foreach ($projects as $value)
                    <tr>
                        <td style="vertical-align: middle; text-align: center" scope="col" >{{$stt++}}</td>
                        <td style="vertical-align: middle; text-align: center" scope="col"><a href="{{ route('listWarehouse', [$value->id,'module' => $module]) }}">{{$value->name}}</a></td>
                        <td style="vertical-align: middle; text-align: center" scope="col">{{$value->description}}</td>
                        <td style="vertical-align: middle; text-align: center" scope="col">{{ \Carbon\Carbon::parse($value->created_at)->format('d/m/Y H:i:s') }}</td>
                        <td style="vertical-align: middle; text-align: center" scope="col">
                            @if (in_array($user->appFunction->id, [3, 5]))
                                <button class="btn btn-sm btn-primary edit-project-model" data-bs-target="#Edit-project" data-bs-toggle="modal" data-id="{{$value->id}}" data-name="{{$value->name}}" data-description="{{$value->description}}">Sửa</button>
                                <button class="btn btn-sm btn-danger delete-project" data-id="{{$value->id}}">Xóa</button>
                            @endif
                        </td>
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
<!-- MODAL THÊM DỰ ÁN-->
    <div class="modal fade" id="smallModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Thêm dự án</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    {{-- Thêm trường input ẩn cho segmentId --}}
                    <input type="text" id="Segment_id" value="{{ $segment->id ?? '' }}" hidden class="form-control">
                    
                    {{-- Giữ nguyên trường input ẩn cho brandId nếu bạn vẫn muốn truyền nó --}}
                    <input type="text" id="Brand_id" value="{{ $brand->id ?? '' }}" hidden class="form-control">
                    
                    <label for="inputText" class="col-sm-2 col-form-label">Tên:</label>
                    <div class="col-sm-10">
                    <input type="text" id="name" class="form-control">
                    </div>
                    <label for="inputText" class="col-sm-2 col-form-label mt-2">Mô tả:</label>
                    <div class="col-sm-10">
                    <textarea type="text" id="description" class="form-control mt-2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="button" class="btn btn-primary add-project">Thêm</button>
            </div>
        </div>
        </div>
    </div>

<!-- MODAL SỬA DỰ ÁN--->
    <div class="modal fade" id="Edit-project" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Sửa dự án</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <input type="text" id="projectId" value="" hidden class="form-control">
                    <label for="inputText" class="col-sm-2 col-form-label">Tên:</label>
                    <div class="col-sm-10">
                    <input type="text" id="edit_name" class="form-control">
                    </div>
                    <label for="inputText" class="col-sm-2 col-form-label mt-2">Mô tả:</label>
                    <div class="col-sm-10">
                    <textarea type="text" id="edit_description" class="form-control mt-2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="button" class="btn btn-primary edit-project">Sửa</button>
            </div>
        </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            $('.add-project').on('click', function() {
                let segmentId = $('#Segment_id').val(); // Lấy segment ID từ input ẩn
                let name = $('#name').val();
                let description = $('#description').val();

                $.ajax({
                    type: "POST",
                    url: "{{ route('addProject') }}",
                    data: {
                        segment_id: segmentId, // Gửi segment ID thay vì brand ID
                        name: name,
                        description: description,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Thành công!',
                                'Dự án đã được thêm thành công!',
                                'success'
                            ).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire(
                                'Lỗi!',
                                'Có lỗi xảy ra khi thêm dự án.',
                                'error'
                            );
                        }
                    }
                });
            });
        });
    </script>


    <script>
        $(document).ready(function() {
            // Sự kiện click vào nút sửa
            $('.edit-project-model').on('click', function() {
                let projectId = $(this).data('id');
                let name = $(this).data('name');
                let description = $(this).data('description');

                $('#projectId').val(projectId);
                $('#edit_name').val(name);
                $('#edit_description').val(description);
            });

            // Sự kiện click vào nút lưu trong modal
            $('.edit-project').on('click', function() {
                let projectId = $('#projectId').val();
                let name = $('#edit_name').val();
                let description = $('#edit_description').val();

                $.ajax({
                    type: "POST",
                    url: "{{ route('editProject') }}",
                    data: {
                        id: projectId,
                        name: name,
                        description: description,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire(
                                'Thành công!',
                                'Dự án đã được sửa thành công!',
                                'success'
                            ).then((result) => {
                                if (result.isConfirmed) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire(
                                'Lỗi!',
                                'Có lỗi xảy ra khi sửa dự án.',
                                'error'
                            );
                        }
                    }
                });
            });
        });
    </script>

    <script>
         $(document).ready(function() {
            $('.delete-project').on('click', function() {
                let projectId = $(this).data('id');

                Swal.fire({
                    title: 'Bạn chắc chắn muốn xóa?',
                    text: "Dự án sẽ bị xóa vĩnh viễn!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy bỏ'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            type: "POST",
                            url: "{{ route('deleteProject') }}",
                            data: {
                                id: projectId,
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Đã xóa!',
                                        'Dự án đã được xóa thành công.',
                                        'success'
                                    ).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire(
                                        'Lỗi!',
                                        'Có lỗi xảy ra khi xóa dự án.',
                                        'error'
                                    );
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
