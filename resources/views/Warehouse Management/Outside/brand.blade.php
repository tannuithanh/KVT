@extends('Layout.app')

@section('title')
    Thương hiệu
@endsection

@section('content')
    <div class="pagetitle">
        <h1>Thương hiệu</h1>
        <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Trang chủ</a></li>
            <li class="breadcrumb-item">Nhập kho</li>
            <li class="breadcrumb-item active">Thương hiệu</li>
        </ol>
        </nav>
    </div>

    <section class="section">
    <div class="row">
    <div class="col-lg-12">
        <div class="card">
        <div class="card-body">
            <h5 class="card-title">Danh sách thương hiệu</h5>
            <table class="table table-borderless table-bordered">
            <thead>
                <tr>
                    <th style="text-align: center; background-color: #12236d; color: white" scope="col" >STT</th>
                    <th style="text-align: center; background-color: #12236d; color: white"  scope="col">Thương hiệu</th>
                    <th style="text-align: center; background-color: #12236d; color: white"  scope="col">Số lượng dự án</th>
                    <th style="text-align: center; background-color: #12236d; color: white"  scope="col">Số lượng vật tư</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($brands as $value )
                    <tr>
                        <td style="text-align: center" >{{$value->id}}</td>
                        <td style="text-align: center" ><a href="{{ route('listProject', $value->id) }}">{{$value->name}}</a></td>
                        <td style="text-align: center" >{{$value->projects_count}}</td>
                        <td style="text-align: center" >{{$value->totalSupplies}}</td>
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
@endsection

@section('script')
@endsection
