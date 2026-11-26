# 银行字典表

支付/提现场景常用的银行字典：名称、别名、图标、首字母。

**表名：`bank`**

```php
$builder = $this->builder('银行字典表');
$builder->id();
$builder->string('name', '银行名称', 60);
$builder->string('alias', '别名', 15)->default('');
$builder->string('icon', '图标')->default('');
$builder->string('first_letter', '首字母');
$builder->tinyInteger('status', '状态 0 禁用 forbid 1 启用 open')->default(1);
$builder->smallInteger('sort', '排序 正序')->default(99);
$this->create($builder);
```
