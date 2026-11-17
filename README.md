# Phinx

### 简介

基于phinx:0.14.0数据库迁移工具

composer.json不依赖phinx:0.14.0,而是将phinx跟其依赖的包提取到phinx目录下,只保留了基础的功能

[gitee](https://gitee.com/magein/php-migration) | [composer](https://packagist.org/packages/magein/migration)

空间类型字段mysql版本最好是>=5.7

### 修改

1. 可以链式调用
2. 已注册命令原样透传给 Phinx；`run` 是 `migrate` 的快捷写法（可带 `-e`）
3. 省略 create 命令，直接写迁移类名即可创建（如 `create_user`），自动转驼峰并注入本包骨架

### 安装

> php:^7.4

```
composer require magein/migration
```

#### 配置文件

只保留了.php的解析, .yml格式删除

> 根目录生成phinx.php

完整配置模板与字段说明见 [docs/phinx.config.md](docs/phinx.config.md)；`phinx.php` 含本机数据库信息，不纳入版本控制，按模板自行创建。

#### 命令行

所有命令都通过 `bin/migrate` 执行。第一个参数是已注册命令（含 `create`、`seed:create`、`seed:run`）或 `-` 开头的全局选项时，原封不动透传给 Phinx；要带 `-c / -p / -t / -d` 等参数就用原生写法。

**1. 创建迁移文件**

省略 `create` 直接写类名：下划线转驼峰并校验合法性，自动注入本包骨架。不补 `create_` 前缀 —— `create_` 开头走建表骨架，其余走变更骨架（表名留空手填），建议统一用 `create_` 开头。

```shell
php migrate create_user            # CreateUser，建表
php migrate CreateUser             # 等价写法
php migrate append_user_profile    # AppendUserProfile，新增字段
php migrate update_user            # UpdateUser，修改字段
php migrate remove_user_legacy     # RemoveUserLegacy，删除字段
php migrate rename_user_to_member  # RenameUserToMember，表重命名
```

显式写 `create` 即 Phinx 原生命令：类名须是 CamelCase、生成默认骨架、不注入 `--class`，可追加选项：

```shell
php migrate create CreateUser
php migrate create CreateUser --path=./database/migrations
php migrate create CreateUser --class=MyCreator
```

自定义骨架用 `--class=你的类`：类实现 `Phinx\Migration\CreationInterface`，由 `getMigrationTemplate()` 返回模板内容。`--template` 与 `--class` 互斥，本包不支持该写法。

非命令的裸词都当作迁移类名，`user` 会生成 `User`（变更骨架）。只有 `Create` 开头的类名自动推断表名，其余 `protected $name` 为空需要手填；`up()` 里的字段定义和 `down()` 都要按实际迁移手写。

**2. 执行迁移**

```shell
php migrate migrate                            # 执行迁移
php migrate run                                # 任意位置出现 run 都执行 migrate
php migrate run -e production                  # run 只保留 -e / --environment
php migrate migrate --target 20250801000001    # 原生写法可带任意参数
```

**3. 回滚与断点**

```shell
php migrate rollback -e production                             # 回滚最后一个迁移
php migrate rollback -e production --target 20250801000001     # 撤掉该版本之后的所有迁移（该版本保留）
php migrate rollback -e production --target 20250801000001 -x  # 只打印 SQL，不执行
php migrate rollback -e production --target 0                  # 全部回滚，遇断点会停（-f 忽略）
php migrate rollback -e production --date 20250801             # 按日期回滚
php migrate breakpoint -t 20250801000001 -e production         # 切换断点，rollback 到该版本即停
```

> **回滚提示**（容易记混，随手查）
> - `--target V` = **保留到 V**，不是「回滚 V」：从最新往回撤，遇到 V 即停，V 本身不回滚；V 之后只有 1 个已执行迁移时，结果与不带参数相同，看着像没生效。想连 V 一起撤就填它前一个版本号。
> - 填一个**从未执行过**的版本号会一路撤光所有已执行迁移；拿不准范围先加 `-x`（`--dry-run`）只打印 SQL。
> - `--date D` 是「回滚到时间点 D 之前」，比 `--target` 更批量；默认按**迁移文件名时间戳**比较（`version_order => creation`），不是执行时间。与 `--target` 同时给时 **`--date` 优先、`--target` 被静默忽略**。
> - **不能只撤中间某一个版本**：回滚按逆序，它之后的迁移会被连带撤掉，重新 `migrate` 又会把该版本跑回来。要抵消那次变更就写个新迁移做反向操作，别回滚。

> `breakpoint` 不带 `--set` / `--unset` 时是**切换**（已有就取消、没有就打上），断点记在 `phinx_log`：`rollback` 遇到带断点的版本会停止，不会继续回滚更早的迁移。显式设置用 `--set`、取消用 `--unset`、清空用 `--remove-all`；`rollback --force` 可忽略断点。

**4. 状态、seed 与帮助**

```shell
php migrate status -e production
php migrate seed:create UserSeeder
php migrate seed:run -e production
php migrate list
php migrate --help
```

便捷写法（裸词类名、`run`）只保留 `-e` / `--environment`，其余参数丢弃；要指定别的参数就用原生命令，选项怎么写由 Phinx 自己解析。自定义配置文件用 `--configuration` 指定。

#### 调用

> 在根目录生成migrate

```php
include('vendor/autoload.php');

$input = new \magein\migration\PhinxInput();
$app = new \Phinx\Console\PhinxApplication();
$app->run($input->init());
```

`init()` 返回 `Phinx\Component\Input\ArgvInput`（实现 `InputInterface`），必须把它传给 `run()`；也可以用 `$input->setClass()` / `$input->setConfiguration()` 注入自定义创建类与配置路径。

#### 迁移文件

迁移类继承 `magein\migration\Migrator`。这一节只列**最容易踩的规则**；三个方法的完整区别、表级操作速查表、模板对应关系与可复制的迁移示例统一放在 [docs/README.md](docs/README.md)。

> **⚠️ 重点：加 / 删索引、外键等表级动作，最后用 `save()` 还是 `update()`？**
>
> | 写在哪 | 结尾 |
> |---|---|
> | `change()` | **`->update()`**（禁止 `->save()`） |
> | `up()` / `down()` | `->save()` |
>
> `change()` 里写 `$this->table(...)->addIndex(...)->update()`；`up()` / `down()` 里写 `...->addIndex(...)->save()`。`addIndex` / `addForeignKey` 在 `change()` 中由 Phinx 自动反转，用 `->update()` 提交即可。`save()` 靠 `hasTable()` 实时判断，升级与回滚两次结果不同，放进 `change()` 反转会留下空表。

**另外三条必记**

- `change()` 提交建表与改列只用 `create()` / `update()`，**不要调用 `save()`**；删列、改名、删表等无法自动反转的操作改写 `up()` / `down()`，`down()` 不能省。
- `create($builder, $created_at = true, $soft_delete = true)`：第二参是时间戳开关，第三参才是软删。要「保留时间戳、不要软删」写 `$this->create($builder, true, false)`；只传 `false` 去掉的是时间戳。时间列非空无默认、**不使用 `CURRENT_TIMESTAMP`**，由应用层维护（原因见 [docs/README.md](docs/README.md)）。
- `Builder` **不提供任何索引 / 外键 API**，组合索引、唯一索引、外键一律用 `$this->table(实际表名)->addIndex()` / `addForeignKey()`；列级 `->index()` / `->unique()` 仍可用，但只支持单列。

`$this->table()` 的第一个参数是**实际表名**，不是 `Builder`。`Builder::change()` 必须给出列名与新类型，且是整列重建 —— 要保留的 `default()` / `nullable()` / `precision()` / `scale()` 都要一起写全，否则回落默认值。

字段类型原语、预设分组、表选项、常见表结构场景、完整速查表与迁移示例见 [docs/README.md](docs/README.md)；`phinx.php` 配置见 [docs/phinx.config.md](docs/phinx.config.md)。

### 测试

依赖 `phpunit/phpunit ^9.6`（在 `require-dev`），SQLite 用例需要 `pdo_sqlite` 扩展。

```shell
composer test                                              # 全量
php vendor/phpunit/phpunit/phpunit --filter BuilderTest    # 单个测试类
```

| 文件                                | 覆盖                            |
|-----------------------------------|-------------------------------|
| `tests/BuilderTest.php`           | 表选项归一、重复列名、列定义 SQL、预设组、索引选项归一 |
| `tests/SchemaTest.php`            | 表级选项白名单与默认值、各 setter          |
| `tests/PhinxConfigTest.php`       | 配置递归合并                        |
| `tests/PhinxInputTest.php`        | CLI 参数归一、`--class` 注入、非法输入    |
| `tests/PhinxCreationTest.php`     | 模板表名推断                        |
| `tests/SqliteIntegrationTest.php` | SQLite 内存库真实建表、索引、外键、删表       |
| `tests/support/`                  | 列定义 SQL 与列选项探针                |

CI 见 `.github/workflows/ci.yml`：PHP 7.4 / 8.0 / 8.2 三档执行 `vendor/bin/phpunit`。
