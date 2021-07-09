<div class="box box-info">
  <div class="box-header with-border">
    <h3 class="box-title">订单流水号：{{ $order->no }}</h3>
    <div class="box-tools">
      <div class="btn-group float-right" style="margin-right: 10px">
        <a href="{{route('admin.orders.index') }}" class="btn btn-sm btn-default"><i class="fa fa-list"></i> 列表</a>
      </div>
      <div class="btn-group pull-right" style="margin-right: 5px">
        <a href="/admin/orders/{{$order->id}}/edit" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> 编辑</a>
      </div>
    </div>
  </div>
  <div class="box-body">
    <table class="table table-bordered">
      <tbody>
      <tr>
        <td>买家：</td>
        <td>{{ $order->user->nickname}}<br/>手机号：{{ $order->user->phone}}</td>
        <td>支付时间：</td>
        @if($order->paid_at)
        <td>{{ $order->paid_at->format('Y-m-d H:i:s') }}</td>
        @endif
      </tr>
      <tr>
        <td>支付方式：</td>
        @if($order->payment_method)
        <td>{{ \App\Models\Order::$paymentMethodMap[$order->payment_method] }}</td>
        @endif
        <td>支付渠道单号：</td>
        <td>{{ $order->payment_no }}</td>
      </tr>
      <tr>
        <td>收货地址</td>
        <td colspan="3">{{ $order->address['0'] }} {{ $order->address['1'] }} {{ $order->address['2'] }}</td>
      </tr>
      <tr>
        <td rowspan="{{ $order->items->count() + 1 }}">商品列表</td>
        <td>商品名称</td>
        <td>单价</td>
        <td>数量</td>
      </tr>
      @foreach($order->items as $item)
        <tr>
          <td>{{ $item->product->title }} {{ $item->productSku->title }}</td>
          <td>￥{{ $item->price }}</td>
          <td>{{ $item->amount }}</td>
        </tr>
      @endforeach
      <tr>
        <td>订单金额：</td>
        <td>￥{{ $order->total_amount }}</td>
        <td>发货状态：</td>
        <td>{{ \App\Models\Order::$shipStatusMap[$order->ship_status] }}</td>
      </tr>

      <!-- 订单发货开始 -->
      <!-- 如果订单未发货，展示发货表单 -->
      {{--@if($order->ship_status === \App\Models\Order::SHIP_STATUS_PENDING)
        <tr>
          <td colspan="4">
            <form action="{{route('admin.orders.ship', [$order->id]) }}" method="post" class="form-inline">
              <!-- 别忘了 csrf token 字段 -->
              {{ csrf_field() }}
              <div class="form-group {{ $errors->has('express_company') ? 'has-error' : '' }}">
                <label for="express_company" class="control-label">物流公司</label>
                <input type="text" id="express_company" name="express_company" value="" class="form-control" placeholder="输入物流公司">
                @if($errors->has('express_company'))
                  @foreach($errors->get('express_company') as $msg)
                    <span class="help-block">{{ $msg }}</span>
                  @endforeach
                @endif
              </div>
              <div class="form-group {{ $errors->has('express_no') ? 'has-error' : '' }}">
                <label for="express_no" class="control-label">物流单号</label>
                <input type="text" id="express_no" name="express_no" value="" class="form-control" placeholder="输入物流单号">
                @if($errors->has('express_no'))
                  @foreach($errors->get('express_no') as $msg)
                    <span class="help-block">{{ $msg }}</span>
                  @endforeach
                @endif
              </div>
              <button type="submit" class="btn btn-success" id="ship-btn">发货</button>
            </form>
          </td>
        </tr>
      @else--}}
        <!-- 否则展示物流公司和物流单号 -->
     {{--  <tr>
          <td>物流公司：</td>
          <td>{{ $order->ship_data['express_company'] }}</td>
          <td>物流单号：</td>
          <td>{{ $order->ship_data['express_no'] }}</td>
        </tr>
      @endif--}}
      <!-- 订单发货结束 -->

      {{--@if($order->status !== \App\Models\Order::STATUS_PENDING)
        <tr>
          <td>退款状态：</td>
          <td colspan="2">{{ \App\Models\Order::$statusMap[$order->status] }}，理由：{{ $order->extra['refund_reason'] }}</td>
          <td>
            <!-- 如果订单退款状态是已申请，则展示处理按钮 -->
            @if($order->refund_status === \App\Models\Order::STATUS_PENDING)
              <button class="btn btn-sm btn-success" id="btn-refund-agree">同意</button>
              <button class="btn btn-sm btn-danger" id="btn-refund-disagree">不同意</button>
            @endif
          </td>
        </tr>
      @endif--}}
      </tbody>
    </table>
    <div>

      @if(!empty($order->extra['many_images']))
        @foreach($order->extra['many_images'] as $image)
          <h4>退款图片:</h4>
          <img src="https://hair.test/storage/{{$image}}" width="120px" height="120px" style="margin-left: 15px">
        @endforeach
      @endif
    </div>
  </div>
</div>
