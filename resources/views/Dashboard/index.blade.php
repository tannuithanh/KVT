@extends('Layout.app')
@section('title')
    Trang chủ | Quản lý kho
@endsection

@section('content')
<section class="section dashboard">
    <div class="row">
      <!-- Left side columns -->
      <div class="col-lg-8">
        <div class="row">
          <!-- Recent Sales -->
          <div class="col-12">
            <div class="card recent-sales overflow-auto">



              <div class="card-body">
                <h5 class="card-title">Thông tin nhập kho <span>| Hôm nay</span></h5>

                <table class="table table-borderless datatable">
                  <thead>
                    <tr>
                      <th scope="col">STT</th>
                      <th scope="col">Dự án</th>
                      <th scope="col">Tên vật tư</th>
                      <th scope="col">Mã vật tư</th>
                      <th scope="col">Số lượng</th>
                      <th scope="col">Thực nhận</th>
                      <th scope="col">trạng thái</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td scope="row"><a href="#">1</a></td>
                      <td>BT2112</td>
                      <td><a href="#" class="text-primary">At praesentium minu</a></td>
                      <td>BCS5021</td>
                      <td>1</td>
                      <td>1</td>
                      <td><span class="badge bg-success">Nhận đủ số lượng</span></td>
                    </tr>
                    <tr>
                      <td scope="row"><a href="#">2</a></td>
                      <td>BT2114</td>
                      <td><a href="#" class="text-primary">At praesentium minu</a></td>
                      <td>BCS5021</td>
                      <td>2</td>
                      <td>2</td>
                      <td><span class="badge bg-success">Nhận đủ số lượng</span></td>
                    </tr>
                    <tr>
                      <td scope="row"><a href="#">3</a></td>
                      <td>BT2112</td>
                      <td><a href="#" class="text-primary">At praesentium minu</a></td>
                      <td>BCS5021</td>
                      <td>3</td>
                      <td>3</td>
                      <td><span class="badge bg-success">Nhận đủ số lượng</span></td>
                    </tr>
                    <tr>
                      <td scope="row"><a href="#">4</a></td>
                      <td>B01D</td>
                      <td><a href="#" class="text-primary">At praesentium minu</a></td>
                      <td>BCS5021</td>
                      <td>4</td>
                      <td>2</td>
                      <td><span class="badge bg-warning">Nhận thiếu</span></td>
                    </tr>
                    <tr>
                      <td scope="row"><a href="#">5</a></td>
                      <td>BT2111</td>
                      <td><a href="#" class="text-primary">At praesentium minu</a></td>
                      <td>BCS5021</td>
                      <td>2</td>
                      <td>2</td>
                      <td><span class="badge bg-success">Nhận đủ số lượng</span></td>
                    </tr>
                    <tr>
                      <td scope="row"><a href="#">6</a></td>
                      <td>BT2112</td>
                      <td><a href="#" class="text-primary">At praesentium minu</a></td>
                      <td>BCS5021</td>
                      <td>2</td>
                      <td>1</td>
                      <td><span class="badge bg-warning">Nhận thiếu</span></td>
                    </tr>
                    <tr>
                      <td scope="row"><a href="#">7</a></td>
                      <td>BT2115</td>
                      <td><a href="#" class="text-primary">At praesentium minu</a></td>
                      <td>BCS5021</td>
                      <td>1</td>
                      <td>1</td>
                      <td><span class="badge bg-success">Nhận đủ số lượng</span></td>
                    </tr>
                    <tr>
                      <td scope="row"><a href="#">8</a></td>
                      <td>BT2114</td>
                      <td><a href="#" class="text-primary">At praesentium minu</a></td>
                      <td>BCS5021</td>
                      <td>1</td>
                      <td>1</td>
                      <td><span class="badge bg-success">Nhận đủ số lượng</span></td>
                    </tr>
                  </tbody>
                </table>

              </div>

            </div>
          </div><!-- End Recent Sales -->

          <!-- Recent Sales -->
          <div class="col-12">
            <div class="card recent-sales overflow-auto">



              <div class="card-body">
                <h5 class="card-title">Thông tin nhập kho <span>| Hôm nay</span></h5>

                <table class="table table-borderless datatable">
                  <thead>
                    <tr>
                      <th scope="col">STT</th>
                      <th scope="col">Dự án</th>
                      <th scope="col">Tên vật tư</th>
                      <th scope="col">Mã vật tư</th>
                      <th scope="col">Số lượng</th>
                      <th scope="col">Nguời nhận</th>

                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row"><a href="#">1</a></th>
                      <td>BT2112</td>
                      <td><a href="#" class="text-primary">At praesentium minu</a></td>
                      <td>BCS5021</td>
                      <td>1</td>
                      <td>Nguyễn Minh Tân</td>
                    </tr>

                  </tbody>
                </table>

              </div>

            </div>
          </div><!-- End Recent Sales -->
        </div>
      </div><!-- End Left side columns -->

      <!-- Right side columns -->
      <div class="col-lg-4">


        <!-- Website Traffic -->
        <div class="card">

          <div class="card-body pb-0">
            <h5 class="card-title">Thương hiệu <span>| tổng số vật tư đang có tại kho</span></h5>

            <div id="trafficChart" style="min-height: 400px;" class="echart"></div>

            <script>
              document.addEventListener("DOMContentLoaded", () => {
                echarts.init(document.querySelector("#trafficChart")).setOption({
                  tooltip: {
                    trigger: 'item'
                  },
                  legend: {
                    top: '5%',
                    left: 'center'
                  },
                  series: [{
                    name: 'Số vật tư',
                    type: 'pie',
                    radius: ['50%', '75%'],
                    avoidLabelOverlap: false,
                    label: {
                      show: false,
                      position: 'center'
                    },
                    emphasis: {
                      label: {
                        show: true,
                        fontSize: '18',
                        fontWeight: 'bold'
                      }
                    },
                    labelLine: {
                      show: false
                    },
                    data: [{
                      value: 2048,
                      name: '2 BÁNH'
                    },
                    {
                      value: 735,
                      name: 'ROYAL'
                    },
                    {
                      value: 2680,
                      name: 'TẢI'
                    },
                    {
                      value: 1364,
                      name: 'DU LỊCH'
                    },
                    {
                      value: 1100,
                      name: 'BUS'
                    },
                    {
                      value: 1250,
                      name: 'NÔNG NGHIỆP'
                    }
                    ]
                  }]
                });
              });
            </script>

          </div>
        </div><!-- End Website Traffic -->

      </div>
  </section>

@endsection
