# 用户收货地址

一个用户多条收货地址，含省市区、详细地址、邮编、经纬度与默认地址标记。

**表名：`user_address`**

> 同类片段「省市区」见 [省市区地址片段](../parts/parts.md)。

```php
$builder = $this->builder('用户收货地址');
$builder->id();
$builder->unsignedInteger('user_id', '用户id');
$builder->string('name', '收货人', 60);
$builder->char('phone', '联系电话', 18);
$builder->string('province', '省份', 30)->default('');
$builder->string('city', '城市', 30)->default('');
$builder->string('district', '县区', 30)->default('');
$builder->string('address', '地址', 60)->default('');
$builder->string('postcode', '邮编', 30)->default('');
$builder->string('location', '经纬度', 60)->default('');
$builder->tinyInteger('is_default', '默认地址')->default(0);
$this->create($builder);
```
