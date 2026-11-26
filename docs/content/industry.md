# 行业字典表

树形行业分类，结构与 [分类表](category.md) 相同，只是名称字段带业务语义。

**表名：`industry`**

```php
$builder = $this->builder('行业字典表');
$builder->id();
$builder->integer('parent_id', '上级行业')->default(0);
$builder->string('name', '行业名称', 60);
$builder->string('alias', '别名', 15)->default('');
$builder->tinyInteger('status', '状态 0 禁用 forbid 1 启用 open')->default(1);
$builder->smallInteger('sort', '排序 正序')->default(99);
$this->create($builder);
```
