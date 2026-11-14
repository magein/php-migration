# Phinx

### 简介

基于phinx:0.14.0数据库迁移工具

composer.json不依赖phinx:0.14.0,而是将phinx跟其依赖的包提取到phinx目录下,只保留了基础的功能

[gitee](https://gitee.com/magein/php-migration) | [composer](https://packagist.org/packages/magein/migration)

空间类型字段mysql版本最好是>=5.7

### 修改

1. 可以链式调用
2. 优化了命令行的参数位置(一些命令不在局限于位置)
3. 可以省略migrate参数,支持小写,下划线的方式创建

### 安装

> php:^7.1

```
composer require magein/migration
```

#### 配置文件

只保留了.php的解析, .yml格式删除

> 根目录生成phinx.php

```php
return \magein\migration\PhinxConfig::config(
    [
        'environments' => [
            'default_migration_table' => 'phinx_log',
            'default_environment' => 'development',
            'production' => [
                'adapter' => 'mysql',
                'host' => 'localhost',
                'name' => 'production_db',
                'user' => 'root',
                'pass' => '',
                'port' => 3306,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'development' => [
                'adapter' => 'mysql',
                'host' => '127.0.0.1',
                'name' => 'hyt',
                'user' => 'root',
                'pass' => '',
                'port' => 3306,
                'charset' => 'utf8',
            ],
            'testing' => [
                'adapter' => 'sqlite',
                'name' => './tests/_data/database',
                'memory' => false
            ]
        ],
    ]
);
```

#### phinx

这里是phinx默认的命令(可参考官网的命令)

```shell
php phinx create CreateUserTable
php phinx migrate
php phinx -e migrate
php phinx rollback
php phinx seed:create
php phinx seed:run
```

#### 调用

> 在根目录生成migrate

```php
include('vendor/autoload.php');

$input = new \magein\migration\PhinxInput();
$app = new \Phinx\Console\PhinxApplication();
$app->run($input);
```

```php
// 创建表
php migrate user
// 新增字段 --column 可选
php migrate user_append_xx --column xx,xx,xxx
// 移除字段 --column 可选
php migrate user_remove --column xx,xx,xxx
// user表重新命名
php migrate user_rename
// 更新字段
php migrate user_update
```

表字段

```php

protected $name = 'user';

public function change()
{
    $builder = $this->builder('表描述');
    $builder->id();
    $builder->string('username', '登录账号', 30)->length(64)->unique();
    $builder->char('password', '登录账号', 32);
    $builder->integerTiny('from', '来源 1 注册 2 小程序')->default(1);
    $builder->integerSmall('age', '年龄')->nullable();
    $builder->integer('integral', '积分')->default();
    $builder->date('birthday', '生日')->nullable();
    $builder->dateTime('last_login_time', '最后一次登录时间')->nullable();
    $builder->time('notify')->comment('通知时间')->nullable();
    
    // 更新字段
    $builder->change();

    $builder->innoDB();
    $this->create($builder, false);
}

```

#### 执行

> run migrate create

```shell

php migrate run
php migrate migrate
php migrate user run
php migrate run  user
php migrate rollback user
php migrate user rollback 

```

当命令行包含run/migrate/rollback的时候将执行命令,不在区分顺序

### 内置字段

#### 常规字段

```php
$builder = $this->builder('');
$builder->id();

// 用户
$builder->user->username();
$builder->user->password();
$builder->basic->name();
$builder->user->nickname();
$builder->contact->phone();
$builder->contact->email();
$builder->user->gender();
$builder->user->age();
$builder->user->birthday();
// 民族
$builder->user->ethnicId();
// 籍贯
$builder->user->nativePlace();
// 用户可积累、兑换的积分系统
$builder->user->points();
// 分数或得分
$builder->user->score();
// 证件类型  身份证 港澳通行证  台胞证等
$builder->user->idType();
// 证件号码
$builder->user->idNumber();

// 常规
$builder->basic->type();
$builder->article->title();
$builder->article->content();
$builder->basic->remark();
$builder->basic->sort();
$builder->date->beginTime();
$builder->date->startTime();
$builder->date->endTime();
$builder->basic->status();

// 地址
$builder->addr->address();
$builder->addr->province();
$builder->addr->city();
$builder->addr->area();
$builder->addr->district();
$builder->addr->street();
$builder->addr->postcode();
$builder->addr->location();
$builder->addr->lng();
$builder->addr->lat();

```

#### 常见表结构

包不预设整张表——表结构属于项目自己的 migration，迁移文件应当自包含。按业务场景整理好的可复制代码放在 [docs/README.md](docs/README.md)：用户、用户地址、员工、商家、企业、银行、分类、文章、轮播图、行政区划、行业、商品、订单、支付订单、优惠券规则、发票、附件文件等场景，点开即可复制 `change()` 代码。

字段预设本来就是按场景组合使用的：

```php
$builder->id();
$builder->user->username();
$builder->user->password();
$builder->user->nickname();
$builder->contact->phone();
$builder->contact->email();
$builder->basic->status();
$builder->basic->sort();
```