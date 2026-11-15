# 用户收货地址

一个用户多条收货地址，含省市区、详细地址、邮编、经纬度与默认地址标记。

**表名：`user_address`**

> 同类片段「省市区」见 [省市区地址片段](../parts/parts.md)。

```php
$builder = $this->builder('用户收货地址');
$builder->id();
$builder->user->userId();
$builder->basic->name('收货人');
$builder->contact->phone('联系电话', 18);
$builder->addr->province();
$builder->addr->city();
$builder->addr->district();
$builder->addr->address();
$builder->addr->postcode();
$builder->addr->location();
$builder->tinyInteger('is_default', '默认地址')->default(0);
$this->create($builder);
```
