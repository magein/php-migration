# 用户表

账号基础信息：登录名、密码、昵称、头像、手机、邮箱、性别、生日。

**表名：`user`**

> 把下面代码复制到 migration 的 `change()` 中，按需增删列。`id` 需要手写；`created_at / updated_at / deleted_at` 由 `Migrator::create()` 自动注入（不需要软删时传 `false`）。

```php
$builder = $this->builder('用户表');
$builder->id();
$builder->string('username', '登录账号', 30)->unique();
$builder->string('password', '密码');
$builder->string('nickname', '昵称', 60)->default('');
$builder->string('avatar', '头像')->default('');
$builder->char('phone', '手机号码', 11);
$builder->string('email', '邮箱地址', 60)->default('');
$builder->enum('gender', ['0', '1', '2'])->comment('性别 0 未知 secret 1 男 man 2 女 woman')->default('0');
$builder->date('birthday', '出生年月')->nullable();
$this->create($builder);
```
