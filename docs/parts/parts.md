# 常用片段

不是整表，只是几组经常一起出现的列，复制到任意表的 `change()` 里即可。

## 省市区

```php
$builder->addr->province();
$builder->addr->city();
$builder->addr->district();
```

## 省市区 + 详细地址 + 经纬度

```php
$builder->addr->province();
$builder->addr->city();
$builder->addr->district();
$builder->addr->address();
$builder->addr->location();
```

## 状态 + 排序

```php
$builder->basic->status();
$builder->basic->sort();
```

## 生效起止时间

```php
$builder->date->beginTime();
$builder->date->endTime();
```
