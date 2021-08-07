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
      <tr style="color:#000000;font-weight: 900;font-size: 15px">
        <td rowspan="20" style="line-height: 80px">用户昵称<br/>
          @if(!empty($order->user_id))
            {{ $order->user->nickname }}
          @endif
        </td>
        <td>发型师</td>
        <td>服务项目</td>
        <td>预约人数</td>
      </tr>

      <tr style="height: 50px">
        <td style="line-height: 50px">{{ $order->designer->name }}</td>
        <td style="line-height: 50px">{{ $order->service_name}}</td>
        <td style="line-height: 50px">{{$order->num}}</td>
      </tr>
      <tr style="color:#000000;font-weight: 900;font-size: 15px">
        <td>预约手机号</td>
        <td>预约时间</td>
        <td>备注</td>
      </tr>
      <tr style="height: 80px">
        <td style="line-height: 80px">{{$order->phone}}</td>
        <td style="line-height: 80px">{{$order->reserve_date}}</td>
        <td style="word-wrap:break-word;word-break:break-all;" width="450px">{{$order->remark}}</td>

      </tr>
      <tr style="color:#000000;font-weight: 900;font-size: 15px">
        <td>订单总金额</td>
        <td>支付方式</td>
        <td>支付时间</td>
      </tr>
      <tr style="height: 50px">
        <td style="line-height: 50px"> ¥{{$order->money}}</td>
        <td style="line-height: 50px">
          @if($order->payment_method == 1)余额支付@endif
        </td>
        <td style="line-height: 50px">{{$order->paid_at}}</td>
      </tr>
      <tr style="color:#000000;font-weight: 900;font-size: 15px">
        <td>订单状态</td>
        <td>订单类型</td>
        <td>退款状态</td>
        {{--<td>支付时间</td>--}}
      </tr>
      <tr style="height: 50px">
        <td style="line-height: 50px">
          @if($order->status == 1)未支付@endif
          @if($order->status == 3)已支付@endif
        </td>
        <td style="line-height: 50px">
          @if($order->type == 1)线上订单@endif
          @if($order->type == 2)线下订单@endif
        </td>
        <td>{{ \App\Models\ReserveOrder::$refundStatusMap[$order->refund_status] }}</td>
        {{--<td style="line-height: 50px">{{$order->paid_at}}</td>--}}
      </tr>
      </tbody>
    </table>
  </div>
</div>
