# 轮播图表

图片地址、跳转链接、打开方式、生效起止时间、投放场景、排序与状态。

**表名：`carousel`**

```php
$builder = $this->builder('轮播图表');
$builder->id();
$builder->article->title();
$builder->string('url', '文件路径 本地或者远程地址')->default('');
$builder->string('redirect', '跳转地址');
$builder->string('target', '打开方式')->default('_blank');
$builder->date->beginTime();
$builder->date->endTime();
$builder->string('scene', '使用场景 多个逗号隔开')->default('');
$builder->basic->sort();
$builder->basic->status();
$builder->basic->remark();
$this->create($builder);
```
