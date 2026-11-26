# 轮播图表

图片地址、跳转链接、打开方式、生效起止时间、投放场景、排序与状态。

**表名：`carousel`**

```php
$builder = $this->builder('轮播图表');
$builder->id();
$builder->string('title', '标题', 30);
$builder->string('url', '文件路径 本地或者远程地址')->default('');
$builder->string('redirect', '跳转地址');
$builder->string('target', '打开方式')->default('_blank');
$builder->dateTime('begin_time', '开始时间');
$builder->dateTime('end_time', '结束时间');
$builder->string('scene', '使用场景 多个逗号隔开')->default('');
$builder->smallInteger('sort', '排序 正序')->default(99);
$builder->tinyInteger('status', '状态 0 禁用 forbid 1 启用 open')->default(1);
$builder->string('remark', '备注')->default('');
$this->create($builder);
```
