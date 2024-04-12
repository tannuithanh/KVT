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
                <li class="breadcrumb-item">{{ $module }}</li>
                <li class="breadcrumb-item"><a href="{{route('listBrand', ['module' => $module])}}">Thương hiệu</a></li>
            </ol>
        </nav>
    </div>

    <section class="section">
    <div class="row">
    <div class="col-lg-12">
        <div class="card">
        <div class="card-body">
            <h5 class="card-title">Danh sách thương hiệu</h5>
            <div class="table-responsive">
                <table class="table table-borderless table-bordered" style="font-size: 20px">
                    <thead>
                        <tr>
                            <th style="text-align: center; background-color: #12236d; color: white" scope="col">STT</th>
                            <th style="text-align: left; background-color: #12236d; color: white" scope="col">Thương hiệu</th>
                            <th style="text-align: left; background-color: #12236d; color: white" scope="col">Phân khúc</th>
                            <th style="text-align: center; background-color: #12236d; color: white" scope="col">Số lượng dự án</th>
                            <th style="text-align: center; background-color: #12236d; color: white" scope="col">Số lượng vật tư</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $stt = 1; @endphp
                        @foreach ($brands as $brand)
                            @php $brandSegmentsCount = $brand->segments ? $brand->segments->count() : 0; @endphp
                            @foreach ($brand->segments as $index => $segment)
                                <tr>
                                    @if ($index == 0 && $brandSegmentsCount)
                                        <td style="text-align: center;vertical-align: middle;width: 1%;" rowspan="{{ $brandSegmentsCount }}">{{ $stt++ }}</td>
                                        <td style="text-align: left;vertical-align: middle;color: black" rowspan="{{ $brandSegmentsCount }}">{{ $brand->name }}</td>
                                    @endif
                                    <td style="text-align: left;">
                                        <a href="{{ route('listProject', ['segment' => $segment->id, 'module' => $module]) }}">{{ $segment->name }}</a>
                                    </td>
                                    <td style="text-align: center;">{{ $segment->projects ? $segment->projects->count() : 0 }}</td>
                                    <td style="text-align: center;">
                                        @php
                                        $totalSupplies = $segment->projects ? $segment->projects->reduce(function ($carry, $project) {
                                            $projectOrderCount = $project->orders ? $project->orders->count() : 0;
                                            return $carry + ($projectOrderCount ? $project->orders->reduce(function ($carryOrder, $order) {
                                                return $carryOrder + ($order->supplies ? $order->supplies->sum('soluong') : 0);
                                            }, 0) : 0);
                                        }, 0) : 0;
                                        @endphp
                                        {{ $totalSupplies }}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>


                </table>
            </div>


        </div>
        </div>
        </div>
    </div>
    </div>
    </section>
@endsection

@section('script')
@endsection
