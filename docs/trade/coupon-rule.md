# 优惠券规则表

规则定义：名称、说明、类型、生效起止时间、抵扣金额、最低使用金额、可用商品范围。

**表名：`coupon_rule`**

```php
$builder = $this->builder('优惠券规则表');
$builder->id();
$builder->basic->name('优惠券名称');
$builder->article->description();
$builder->basic->type('类型 1 满减 2 折扣 3 固定金额')->default(1);
$builder->date->beginTime();
$builder->date->endTime();
$builder->integer('money', '抵扣金额 单位分')->default(0);
$builder->integer('min_money', '最低使用金额 单位分')->default(0);
$builder->string('goods', '可用商品')->default('');
$builder->basic->status();
$this->create($builder);
```
