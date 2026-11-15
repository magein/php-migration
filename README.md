# Phinx

### 简介

基于phinx:0.14.0数据库迁移工具

composer.json不依赖phinx:0.14.0,而是将phinx跟其依赖的包提取到phinx目录下,只保留了基础的功能

[gitee](https://gitee.com/magein/php-migration) | [composer](https://packagist.org/packages/magein/migration)

空间类型字段mysql版本最好是>=5.7

### 修改

1. 可以链式调用
2. 命令与选项原样透传给 Phinx，`-e` / `--target` / `--configuration` 等参数不再被吞掉
3. 省略 create 命令，直接写驼峰或下划线形式的迁移类名即可创建（如 `create_user`）

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

#### 命令行

所有命令都通过 `bin/migrate` 执行，命令写在第一个参数，选项跟在命令之后并原样透传给 Phinx：

```shell
php migrate migrate                            # 执行迁移
php migrate migrate -e production              # 指定环境
php migrate migrate --target 20250801000001    # 迁移到指定版本
php migrate rollback -e production --target 20250801000001
php migrate status -e production
php migrate breakpoint -t 20250801000001 -e production
php migrate seed:create
php migrate seed:run -e production
php migrate --help
```

`run` 是 `migrate` 的别名。选项只支持 `-x`（值单独跟在后面，如 `-e production`）与 `--name` / `--name=value`（如 `--class=MyCreator`、`--configuration=/path/phinx.php`）两种写法，不支持 `-eproduction` 这类连写值；自定义配置文件可用 `--configuration` 指定。

#### 调用

> 在根目录生成migrate

```php
include('vendor/autoload.php');

$input = new \magein\migration\PhinxInput();
$app = new \Phinx\Console\PhinxApplication();
$app->run($input->init());
```

`init()` 返回 `Phinx\Component\Input\ArgvInput`（实现 `InputInterface`），必须把它传给 `run()`；也可以用 `$input->setClass()` / `$input->setConfiguration()` 注入自定义创建类与配置路径。

迁移类名原样传入，不再自动补 `Create` 前缀或 `Table` 后缀；下划线写法只做命名转换，最终会转成驼峰。文件名由类名生成，动作写在名字里就能一眼看出用途。

```shell
php migrate create_user            # CreateUser，建表
php migrate CreateUser             # 等价写法
php migrate append_user_profile    # AppendUserProfile，新增字段
php migrate remove_user_legacy     # RemoveUserLegacy，删除字段
php migrate rename_user_to_member  # RenameUserToMember，表重命名
php migrate update_user            # UpdateUser，修改字段

# 也可以显式写 create，并在命令后追加选项
php migrate create create_user --template=custom.stub --path=./database/migrations
php migrate create create_user --class=MyCreator
```

只有含大写字母或下划线的裸词才会被当成迁移类名；纯小写单词（如 `user`）一律按命令解析，拼错会直接报「无法识别的命令」，不会误生成迁移文件。选项请放在命令之后，前面「命令行」一节列出的 `-e` / `--target` / `--configuration` 等都会原样透传。

变更模板只给骨架，`protected $name`、`up()` 里的字段定义和 `down()` 都要按实际迁移手写；`down()` 尤其不能省，删列、改列、重命名、删表都无法自动反转。

表字段

字段在 `up()` 里用 `$builder` 声明，完整可运行的迁移见下文「迁移文件推荐写法」与 [docs/README.md](docs/README.md)。注意 `Builder::change()` 必须给出列名与新类型，无参调用会抛异常：

```php
$builder->change('username', 'string', '登录账号');   // 列名 + 新类型 + 注释
```

`down()` 不能省，删列、改列、重命名、删表都无法自动反转。

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

```shell
php migrate migrate                 # 执行迁移
php migrate run                     # run 是 migrate 的别名
php migrate rollback -e production  # 回滚，可加 --target / --date 指定版本
php migrate status -e production    # 查看迁移状态
```

命令必须是第一个参数，选项跟在命令之后；不再支持 `php migrate user run` 这类把动作词放在任意位置的写法。

字段预设、列类型原语、常见表结构与迁移注意事项见 [docs/README.md](docs/README.md)。
