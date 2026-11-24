# 银行字典表

支付/提现场景常用的银行字典：名称、别名、图标、首字母。

**表名：`bank`**

```php
$builder = $this->builder('银行字典表');
$builder->id();
$builder->basic->name('银行名称');
$builder->basic->alias('别名');
$builder->image->icon('图标');
$builder->string('first_letter', '首字母');
$builder->basic->status();
$builder->basic->sort();
$this->create($builder);
```
