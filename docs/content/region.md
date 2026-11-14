# 行政区划表

省市区字典：`code` + `parent_code` 组成层级，附拼音、首字母与层级。

**表名：`region`**

```php
$builder = $this->builder('行政区划表');
$builder->id();
$builder->string('code', '行政区划代码', 12)->unique();
$builder->string('parent_code', '父级 code', 20);
$builder->basic->name();
$builder->string('pinyin', '拼音', 20)->default('');
$builder->string('first_char', '首字母', 20)->default('');
$builder->integerTiny('level', '层级 1 省 2 市 3 县/区')->default(1);
$builder->basic->status();
$builder->basic->sort();
$this->create($builder);
```
