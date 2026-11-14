# 企业表

企业主体信息：名称简称、企业代码、行业规模、联系人、省市区地址。

**表名：`business`**

```php
$builder = $this->builder('企业表');
$builder->id();
$builder->basic->name('企业名称');
$builder->basic->alias('简称');
$builder->string('code', '企业代码', 12)->unique();
$builder->integer('industry', '行业')->default(0);
$builder->goods->attribute();
$builder->string('scale', '规模', 140)->default('');
$builder->contact->contact();
$builder->contact->tel();
$builder->addr->province();
$builder->addr->city();
$builder->addr->district();
$builder->addr->address();
$builder->addr->location();
$builder->basic->status();
$builder->basic->sort();
$builder->basic->remark();
$this->create($builder);
```
