# 行业字典表

树形行业分类，结构与 [分类表](category.md) 相同，只是名称字段带业务语义。

**表名：`industry`**

```php
$builder = $this->builder('行业字典表');
$builder->id();
$builder->integer('parent_id', '上级行业')->default(0);
$builder->basic->name('行业名称');
$builder->basic->alias('别名');
$builder->basic->status();
$builder->basic->sort();
$this->create($builder);
```
