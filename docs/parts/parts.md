# 常用片段

不是整表，只是几组经常一起出现的列，复制到任意表的 `change()` 里即可。

## 省市区

```php
$builder->string('province', '省份', 30)->default('');
$builder->string('city', '城市', 30)->default('');
$builder->string('district', '县区', 30)->default('');
```

## 省市区 + 详细地址 + 经纬度

```php
$builder->string('province', '省份', 30)->default('');
$builder->string('city', '城市', 30)->default('');
$builder->string('district', '县区', 30)->default('');
$builder->string('address', '地址', 60)->default('');
$builder->string('location', '经纬度', 60)->default('');
```

## 状态 + 排序

```php
$builder->tinyInteger('status', '状态 0 禁用 forbid 1 启用 open')->default(1);
$builder->smallInteger('sort', '排序 正序')->default(99);
```

## 生效起止时间

```php
$builder->dateTime('begin_time', '开始时间');
$builder->dateTime('end_time', '结束时间');
```
