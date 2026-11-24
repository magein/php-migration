# 发票表

开票记录：发票类型、抬头、内容、纳税人识别号、关联订单号、开票状态。

**表名：`invoice`**

```php
$builder = $this->builder('发票表');
$builder->id();
$builder->basic->type('发票类型 0 个人 1 公司普通发票 2 专用发票');
$builder->article->title('发票抬头');
$builder->article->content('发票内容');
$builder->string('tax', '纳税人识别号', 60);
$builder->string('order_no', '关联订单编号', 32)->default('');
$builder->basic->status('0 未开票 1 已开 2 作废')->default(0);
$builder->basic->remark();
$this->create($builder);
```
