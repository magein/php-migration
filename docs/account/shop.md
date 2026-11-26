# 商家 / 店铺表

平台型项目的店铺主体：归属用户、店铺名称与图片、联系人、所在地区、状态排序。

**表名：`shop`**

```php
$builder = $this->builder('商家店铺表');
$builder->id();
$builder->unsignedInteger('user_id', '用户id');
$builder->string('name', '店铺名称', 60);
$builder->string('logo', 'logo')->default('');
$builder->string('cover', '封面图')->default('');
$builder->string('contact', '联系人', 30)->default('');
$builder->char('phone', '手机号码', 11);
$builder->string('province', '省份', 30)->default('');
$builder->string('city', '城市', 30)->default('');
$builder->string('district', '县区', 30)->default('');
$builder->string('address', '地址', 60)->default('');
$builder->tinyInteger('status', '状态 0 禁用 forbid 1 启用 open')->default(1);
$builder->smallInteger('sort', '排序 正序')->default(99);
$builder->string('remark', '备注')->default('');
$this->create($builder);
```
