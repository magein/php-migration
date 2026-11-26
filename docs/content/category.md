# 分类表

通用树形分类：`parent_id` 指向上级，配合状态、排序、备注。

**表名：`category`**

```php
$builder = $this->builder('分类表');
$builder->id();
$builder->string('name', '名称', 60);
$builder->integer('parent_id', '上级分类')->default(0);
$builder->tinyInteger('status', '状态 0 禁用 forbid 1 启用 open')->default(1);
$builder->smallInteger('sort', '排序 正序')->default(99);
$builder->string('remark', '备注')->default('');
$this->create($builder);
```
