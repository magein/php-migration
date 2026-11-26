# 优惠券规则表

规则定义：名称、说明、类型、生效起止时间、抵扣金额、最低使用金额、可用商品范围。

**表名：`coupon_rule`**

```php
$builder = $this->builder('优惠券规则表');
$builder->id();
$builder->string('name', '优惠券名称', 60);
$builder->string('description', '描述', 140)->default('');
$builder->tinyInteger('type', '类型 1 满减 2 折扣 3 固定金额')->default(0);->default(1);
$builder->dateTime('begin_time', '开始时间');
$builder->dateTime('end_time', '结束时间');
$builder->integer('money', '抵扣金额 单位分')->default(0);
$builder->integer('min_money', '最低使用金额 单位分')->default(0);
$builder->string('goods', '可用商品')->default('');
$builder->tinyInteger('status', '状态 0 禁用 forbid 1 启用 open')->default(1);
$this->create($builder);
```
