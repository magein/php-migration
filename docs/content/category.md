# 分类表

通用树形分类：`parent_id` 指向上级，配合状态、排序、备注。

**表名：`category`**

```php
$builder = $this->builder('分类表');
$builder->id();
$builder->basic->name();
$builder->integer('parent_id', '上级分类')->default(0);
$builder->basic->status();
$builder->basic->sort();
$builder->basic->remark();
$this->create($builder);
```
