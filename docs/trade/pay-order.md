# 支付订单表

支付流水：支付单号、关联订单号、渠道场景、交易金额、第三方交易号、回调地址与结果。

**表名：`pay_order`**

> 同一业务订单可能重试支付产生多条支付单。若 `OrderPreset::orderNo()` 带唯一索引，这里要换成普通 `string('order_no', ...)` 或不加唯一约束。

```php
$builder = $this->builder('支付订单表');
$builder->id();
$builder->order->payNo();
$builder->string('order_no', '关联订单号', 32)->default('');
$builder->integerTiny('channel', '渠道')->default(1);
$builder->integer('parent_id', '支付平台')->default(0);
$builder->integerTiny('scene', '场景 1 支付订单 2 充值')->default(1);
$builder->user->userId();
$builder->string('subject', '交易主题')->default('');
$builder->string('appid', '收款账号');
$builder->order->tradeNo();
$builder->string('trade_type', '平台交易方式')->default('');
$builder->integer('total_amount', '交易总金额 单位分')->default(0);
$builder->integer('coupon_amount', '优惠金额 单位分')->default(0);
$builder->text('coupon_info', '优惠信息')->nullable();
$builder->string('result', '交易结果')->default('');
$builder->basic->reason('交易失败原因');
$builder->basic->remark('交易备注');
$builder->dateTime('notified_at', '异步通知时间')->nullable();
$builder->string('notify_url', '支付完成后通知的地址')->default('');
$builder->string('return_url', '支付完成后跳转的地址')->default('');
$this->create($builder);
```
