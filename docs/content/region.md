# 行政区划表

省市区字典：`code` + `parent_code` 组成层级，附拼音、首字母与层级。

**表名：`region`**

```php
$builder = $this->builder('行政区划表');
$builder->id();
$builder->string('code', '行政区划代码', 12)->unique();
$builder->string('parent_code', '父级 code', 20);
$builder->string('name', '名称', 60);
$builder->string('pinyin', '拼音', 20)->default('');
$builder->string('first_char', '首字母', 20)->default('');
$builder->tinyInteger('level', '层级 1 省 2 市 3 县/区')->default(1);
$builder->tinyInteger('status', '状态 0 禁用 forbid 1 启用 open')->default(1);
$builder->smallInteger('sort', '排序 正序')->default(99);
$this->create($builder);
```
