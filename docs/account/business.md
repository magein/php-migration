# 企业表

企业主体信息：名称简称、企业代码、行业规模、联系人、省市区地址。

**表名：`business`**

```php
$builder = $this->builder('企业表');
$builder->id();
$builder->string('name', '企业名称', 60);
$builder->string('alias', '简称', 15)->default('');
$builder->string('code', '企业代码', 12)->unique();
$builder->integer('industry', '行业')->default(0);
$builder->string('attribute', '属性')->default('');
$builder->string('scale', '规模', 140)->default('');
$builder->string('contact', '联系人', 30)->default('');
$builder->string('tel', '联系电话', 30)->default('');
$builder->string('province', '省份', 30)->default('');
$builder->string('city', '城市', 30)->default('');
$builder->string('district', '县区', 30)->default('');
$builder->string('address', '地址', 60)->default('');
$builder->string('location', '经纬度', 60)->default('');
$builder->tinyInteger('status', '状态 0 禁用 forbid 1 启用 open')->default(1);
$builder->smallInteger('sort', '排序 正序')->default(99);
$builder->string('remark', '备注')->default('');
$this->create($builder);
```
