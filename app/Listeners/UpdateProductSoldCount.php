<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\OrderItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProductSoldCount implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderPaid  $event
     * @return void
     */
    // Laravel 会默认执行监听器的 handle 方法，触发的事件会作为 handle 方法的参数
    public function handle(OrderPaid $event)
    {
        // 从事件对象中取出对应的订单
        $order = $event->getOrder();
        //预加载商品数据
        $order->load('items.product');
        //循环遍历订单的商品
        foreach ($order->items as $item){
            $product = $item->product;
            //计算对应的商品销售量
            $soldCount = OrderItem::query()
                ->where('product_id',$product->id)
                ->whereHas('order',function ($query){
                    $query->whereNotNull('paid_at'); //关联的订单状态是已支付
                })->sum('amount');
            // 更新商品销售量
            $product->update([
                'sold_count' => $soldCount,
            ]);
        }
    }
}
