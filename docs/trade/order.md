# 订单表

交易主表：订单号、来源渠道、买卖双方、收货地址、各项金额、支付/发货/退款状态与时间、优惠券、发票。

**表名：`order`**

> 收货地址那段是「省市区 + 详细地址 + 经纬度」的内联写法，也可以抽成自己的片段方法。

```php
$builder = $this->builder('订单表');
$builder->id();
$builder->char('order_no', '订单编号', 28)->unique();
$builder->tinyInteger('from', '来源')->default(1);
$builder->tinyInteger('channel', '渠道')->default(1);
$builder->integer('buyer_id', '买家ID');
$builder->string('buyer_name', '买家姓名', 50);
$builder->string('buyer_phone', '买家电话', 20);
$builder->string('receiver_name', '收货人姓名', 50);
$builder->string('receiver_phone', '收货人电话', 20);
$builder->dateTime('received_at', '收货时间')->nullable();
$builder->string('province', '省份', 30)->default('');
$builder->string('city', '城市', 30)->default('');
$builder->string('district', '县区', 30)->default('');
$builder->string('address', '地址', 60)->default('');
$builder->string('location', '经纬度', 60)->default('');
$builder->integer('order_amount', '订单金额 单位分');
$builder->integer('goods_amount', '商品总金额 单位分');
$builder->integer('discount_amount', '优惠金额 单位分')->default(0);
$builder->integer('actual_amount', '实际支付金额 单位分');
$builder->integer('freight_amount', '运费金额 单位分')->default(0);
$builder->integer('refund_amount', '退款金额 单位分')->default(0);
$builder->dateTime('refunded_at', '退款时间')->nullable();
$builder->tinyInteger('order_status', '订单状态 -1 取消 cancel 1 已发货 shipped 7 已完成 finished 9 已退款 refunded')->default(1);
$builder->tinyInteger('pay_status', '支付状态 0 待支付 pending 1 已支付 paid')->default(0);
$builder->dateTime('paid_at', '支付时间')->nullable();
$builder->tinyInteger('pay_method', '支付方式')->default(1);
$builder->integer('coupon_id', '优惠券ID')->default(0);
$builder->integer('coupon_amount', '优惠券抵扣金额 单位分')->default(0);
$builder->tinyInteger('is_invoice', '是否需要发票')->default(0);
$builder->string('delivery_company', '物流公司', 50)->default('');
$builder->string('delivery_no', '物流单号', 50)->default('');
$builder->dateTime('delivery_time', '发货时间')->nullable();
$builder->dateTime('finished_at', '完成时间')->nullable();
$builder->string('remark', '备注')->default('');
$this->create($builder);
```
