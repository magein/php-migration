# 商家 / 店铺表

平台型项目的店铺主体：归属用户、店铺名称与图片、联系人、所在地区、状态排序。

**表名：`shop`**

```php
$builder = $this->builder('商家店铺表');
$builder->id();
$builder->user->userId();
$builder->basic->name('店铺名称');
$builder->image->logo();
$builder->image->cover();
$builder->contact->contact();
$builder->contact->phone();
$builder->addr->province();
$builder->addr->city();
$builder->addr->district();
$builder->addr->address();
$builder->basic->status();
$builder->basic->sort();
$builder->basic->remark();
$this->create($builder);
```
