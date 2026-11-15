# 订单表

交易主表：订单号、来源渠道、买卖双方、收货地址、各项金额、支付/发货/退款状态与时间、优惠券、发票。

**表名：`order`**

> 收货地址那段是「省市区 + 详细地址 + 经纬度」的内联写法，也可以抽成自己的片段方法。

```php
$builder = $this->builder('订单表');
$builder->id();
$builder->order->orderNo();
$builder->tinyInteger('from', '来源')->default(1);
$builder->tinyInteger('channel', '渠道')->default(1);
$builder->order->buyerId();
$builder->order->buyerName();
$builder->order->buyerPhone();
$builder->order->receiverName();
$builder->order->receiverPhone();
$builder->order->receivedAt();
$builder->addr->province();
$builder->addr->city();
$builder->addr->district();
$builder->addr->address();
$builder->addr->location();
$builder->order->orderAmount();
$builder->order->goodsAmount();
$builder->order->discountAmount();
$builder->order->actualAmount();
$builder->order->freightAmount();
$builder->order->refundAmount();
$builder->order->refundedAt();
$builder->order->orderStatus();
$builder->order->payStatus();
$builder->order->paidAt();
$builder->order->payMethod();
$builder->order->couponId();
$builder->order->couponAmount();
$builder->order->isInvoice();
$builder->order->deliveryCompany();
$builder->order->deliveryNo();
$builder->order->deliveryTime();
$builder->order->finishedAt();
$builder->basic->remark();
$this->create($builder);
```
