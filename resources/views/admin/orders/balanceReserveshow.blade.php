<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title">订单流水号：{{ $order->no }}</h3>
    <div class="box-tools">
      <div class="btn-group float-right" style="margin-right: 10px">
        <a href="{{route('admin.balance_orders.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
      </div>
    </div>
  </div>
  <div class="box-body">
    <h3>预约信息</h3>
    <table class="table table-bordered">
      <tbody>
      <tr>
        <td>发型师</td>
        <td>服务项目</td>
        <td>预约人数</td>
      </tr>

      <tr style="height: 50px">
        <td>{{ $order->designer->name }}</td>
        <td>{{ $order->service }}</td>
        <td>{{$order->num}}</td>
      </tr>
      <tr>
        <td>预约手机号:  &nbsp;&nbsp;&nbsp;&nbsp;{{$order->phone}}</td>
      </tr>
      <tr>
        <td></td>
      </tr>
     {{-- @endforeach--}}

      </tbody>
    </table>
  </div>
</div>
