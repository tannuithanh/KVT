@extends('Layout.app')
@section('style')
<style>
  .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }



    .card-title {
        font-size: 1.5rem;
        color: #12236d !important;
        margin-bottom: 1rem;
    }

    .segment-box {
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: background-color 0.3s, box-shadow 0.3s;
    }

    .segment-box:hover {
        background-color: #f1f1f1;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }


    .btn-outline-primary:hover {
        background-color: #12236d;
        color: white;
    }

    @media (max-width: 767px) {
        .card {
            margin-bottom: 20px;
        }
    }
</style>
@endsection

@section('title')
    Thương hiệu
@endsection

@section('content')
    <div class="pagetitle">
        <h1>Thương hiệu</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('dashBoard')}}">Trang chủ</a></li>
                <li class="breadcrumb-item">{{ $module }}</li>
                <li class="breadcrumb-item"><a href="{{route('listBrand', ['module' => $module])}}">Thương hiệu</a></li>
            </ol>
        </nav>
    </div>

    <section class="section">
        <div class="row">
            @foreach ($brands as $brand)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title text-primary">{{ $brand->name }}</h5>
                            @foreach ($brand->segments as $segment)
                                <div class="segment-box mb-3 p-3">
                                    <h6 class="text-dark font-weight-bold">Phân khúc:  <a href="{{ route('listProject', ['segment' => $segment->id, 'module' => $module]) }}" class="">{{ $segment->name }}</a></h6>

                                    <p class="mb-1"><strong>Số lượng dự án:</strong> {{ $segment->projects ? $segment->projects->count() : 0 }}</p>
                                    <p class="mb-1">
                                        @php
                                            $totalSupplies = $segment->projects ? $segment->projects->reduce(function ($carry, $project) {
                                                $projectOrderCount = $project->orders ? $project->orders->count() : 0;
                                                return $carry + ($projectOrderCount ? $project->orders->reduce(function ($carryOrder, $order) {
                                                    return $carryOrder + ($order->supplies ? $order->supplies->sum('soluong') : 0);
                                                }, 0) : 0);
                                            }, 0) : 0;
                                        @endphp
                                        <strong>Số lượng vật tư:</strong> {{ $totalSupplies }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection

@section('script')
@endsection
