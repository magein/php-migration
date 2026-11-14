# 用户表

账号基础信息：登录名、密码、昵称、头像、手机、邮箱、性别、生日。

**表名：`user`**

> 把下面代码复制到 migration 的 `change()` 中，按需增删列。`id` 需要手写；`created_at / updated_at / deleted_at` 由 `Migrator::create()` 自动注入（不需要软删时传 `false`）。

```php
$builder = $this->builder('用户表');
$builder->id();
$builder->user->username();
$builder->user->password();
$builder->user->nickname();
$builder->user->avatar();
$builder->contact->phone();
$builder->contact->email();
$builder->user->gender();
$builder->user->birthday();
$this->create($builder);
```
