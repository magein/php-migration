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

迁移类名原样传入，不再自动补 `Create` 前缀或 `Table` 后缀；下划线写法只做命名转换，最终会转成驼峰。文件名由类名生成，动作写在名字里就能一眼看出用途。

```shell
php migrate create_user            # CreateUser，建表
php migrate CreateUser             # 等价写法
php migrate append_user_profile    # AppendUserProfile，新增字段
php migrate remove_user_legacy     # RemoveUserLegacy，删除字段
php migrate rename_user_to_member  # RenameUserToMember，表重命名
php migrate update_user            # UpdateUser，修改字段
```

变更模板只给骨架，`protected $name`、`up()` 里的字段定义和 `down()` 都要按实际迁移手写；`down()` 尤其不能省，删列、改列、重命名、删表都无法自动反转。

表字段

```php

protected $name = 'user';

public function change()
{
    $builder = $this->builder('表描述');
    $builder->id();
    $builder->string('username', '登录账号', 30)->length(64)->unique();
    $builder->char('password', '登录账号', 32);
    $builder->tinyInteger('from', '来源 1 注册 2 小程序')->default(1);
    $builder->smallInteger('age', '年龄')->nullable();
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

#### 迁移文件推荐写法

新增字段（`AppendUserProfile`）：

```php
use magein\migration\Migrator;

class AppendUserProfile extends Migrator
{
    protected $name = 'user';

    public function up()
    {
        $builder = $this->builder();
        $builder->user->age();
        $builder->string('wechat_openid', '微信 openid', 64)->default('')->after('email');

        $this->update($builder);
    }

    public function down()
    {
        $this->removeColumn('age', 'wechat_openid');
    }
}
```

删除字段（`down()` 里要写出原来的列定义，列才建得回来）：

```php
use magein\migration\Migrator;

class RemoveUserLegacy extends Migrator
{
    protected $name = 'user';

    public function up()
    {
        $this->removeColumn('legacy_flag');
    }

    public function down()
    {
        $builder = $this->builder();
        $builder->tinyInteger('legacy_flag', '历史标记')->default(0);

        $this->update($builder);
    }
}
```

表重命名：

```php
use magein\migration\Migrator;

class RenameUserToMember extends Migrator
{
    protected $name = 'user';

    protected $new_name = 'member';

    public function up()
    {
        $this->table($this->name)->rename($this->new_name)->save();
    }

    public function down()
    {
        $this->table($this->new_name)->rename($this->name)->save();
    }
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

#### 其他字段类型

构建器另提供三个原语，返回值同样是 `Column`，可继续链式调用 `nullable()`、`comment()`、`length()` 等：

```php
$builder->year('graduation_year', '毕业年份');  // YEAR(4)
$builder->json('extra', '扩展数据');             // JSON，需 MySQL 5.7.8+
$builder->blob('payload', '二进制内容');         // BLOB
```

`year()` 由 Phinx 固定生成 `YEAR(4)`；`json()` 依赖 MySQL 5.7.8 起的原生 JSON 类型；`blob()` 默认生成 BLOB，需要更大容量时链式 `->length()` 指定（配合 `Phinx\Db\Adapter\MysqlAdapter::BLOB_*` 常量）。

#### 唯一索引与列长度

`->unique()` 只登记唯一索引，不改动列长度（旧版本会把 string 列截到 191）。191 来自早期默认行格式：`COMPACT` / `REDUNDANT` 下 InnoDB 索引前缀上限 767 字节，`utf8mb4` 每字符最多 4 字节，767 ÷ 4 ≈ 191；`utf8`（mb3）下 767 ÷ 3 ≈ 255，本来就够。

MySQL 5.7.7 起 `innodb_file_format` 默认 Barracuda、`innodb_large_prefix` 默认 ON，5.7 的 `innodb_default_row_format` 默认 `DYNAMIC`，`DYNAMIC` / `COMPRESSED` 的索引前缀上限是 3072 字节；`utf8mb4` 的 `VARCHAR(255)` 占 1020 字节，可以直接建唯一索引。8.0 起这是默认行为。

前提是 InnoDB + `DYNAMIC`。本包默认引擎是 MyISAM（可用 `$builder->innoDB()` 切换），若显式使用 `COMPACT` / `REDUNDANT` 或仍是旧配置，可能遇到 `ERROR 1071 Specified key was too long`；这种情况自行 `->length(191)` 或调整表选项，构建器不会替你改列定义。

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