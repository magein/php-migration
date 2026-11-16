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

> php:^7.4

```
composer require magein/migration
```

#### 配置文件

只保留了.php的解析, .yml格式删除

> 根目录生成phinx.php

完整配置模板与字段说明见 [docs/phinx.config.md](docs/phinx.config.md)；`phinx.php` 含本机数据库信息，不纳入版本控制，按模板自行创建。

#### 命令行

所有命令都通过 `bin/migrate` 执行：**命令必须是第一个参数**，选项跟在命令之后原样透传给 Phinx。下面按实际使用顺序排列。

**1. 创建迁移文件**

迁移类名原样传入，不再自动补 `Create` 前缀或 `Table` 后缀；下划线写法只做命名转换，最终转成驼峰，文件名也由类名生成，动作写在名字里就能一眼看出用途。

```shell
php migrate create_user            # CreateUser，建表
php migrate CreateUser             # 等价写法
php migrate append_user_profile    # AppendUserProfile，新增字段
php migrate update_user            # UpdateUser，修改字段
php migrate remove_user_legacy     # RemoveUserLegacy，删除字段
php migrate rename_user_to_member  # RenameUserToMember，表重命名
```

也可以显式写 `create`，并在命令后追加选项：

```shell
php migrate create create_user
php migrate create create_user --template=custom.stub --path=./database/migrations
php migrate create create_user --class=MyCreator
```

只有含大写字母或下划线的裸词才会被当成迁移类名；纯小写单词（如 `user`）一律按命令解析，拼错会直接报「无法识别的命令」，不会误生成迁移文件。变更模板只给骨架：只有 `Create` 开头的类名会自动推断表名，其余 `protected $name` 为空需要手填；`up()` 里的字段定义和 `down()` 都要按实际迁移手写。

**2. 执行迁移**

```shell
php migrate migrate                            # 执行迁移
php migrate run                                # run 是 migrate 的别名
php migrate migrate -e production              # 指定环境
php migrate migrate --target 20250801000001    # 迁移到指定版本
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

选项只支持 `-x`（值单独跟在后面，如 `-e production`）与 `--name` / `--name=value`（如 `--class=MyCreator`、`--configuration=/path/phinx.php`）两种写法，不支持 `-eproduction` 这类连写值；自定义配置文件用 `--configuration` 指定。不再支持 `php migrate user run` 这类把动作词放在任意位置的写法。

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

本包的迁移类继承 `magein\migration\Migrator`，最终由 `create()` / `update()` / `save()` 之一提交。**用哪个方法、写在哪个生命周期里，直接决定能不能自动回滚**，这是最容易踩的坑。

> **核心规则**（`migrate.stub` / `create.stub` 模板内的提醒）
> 如果希望实现自动升级降级，就把逻辑写在 `change()` 里，最终只调用 `create()` 和 `update()`，**不要调用 `save()`**；创建使用 `create()`、更新使用 `update()`。
> 涉及删列、改列、重命名、删表等无法自动反转的操作，改用 `up()` / `down()` 分开写，`down()` 不能省。

**三个方法的区别**

| 方法                                      | 作用            | Phinx 底层                                                     | 自动追加列                                                                           | 适用                                   |
|-----------------------------------------|---------------|--------------------------------------------------------------|---------------------------------------------------------------------------------|--------------------------------------|
| `create($builder, $soft_delete = true)` | 建新表           | `Table::create()` → 固定补一条 `CreateTable` 动作                   | `created_at`、`updated_at`；`$soft_delete` 为 `true` 时再加 `deleted_at`，传 `false` 不加 | 建表                                   |
| `update($builder)`                      | 改已存在的表        | `Table::update()` → 只执行列 / 索引动作，不建表                          | 无                                                                               | 加列、改列、加索引                            |
| `save($builder)`                        | 运行时自己判断建表还是改表 | `Table::save()` → `hasTable()` 为真走 `update()`，否则走 `create()` | **无**（时间戳只在 `create()` 里补）                                                      | `up()` / `down()`；**`change()` 里禁用** |

**为什么 `change()` 里不能用 `save()`**

Phinx 回滚 `change()` 时，会用代理适配器重跑一遍 `change()`、记录动作再反序回放（`CreateTable` → `DropTable`、`AddColumn` → `RemoveColumn`）。`create()` / `update()` 每次都产生同一组动作，反转稳定；`save()` 靠 `hasTable()` 实时判断：升级时表还不存在走 `create()`，回滚时表已存在走 `update()`，两次动作不一致 —— 本该整表删除（`DropTable`），结果只删掉几列（`RemoveColumn`）、把空表留下。

同理，任何依赖 `save()` 运行时判断的表级操作都不要放进 `change()`，一律写到 `up()` / `down()` 里。本包不再为这些操作提供封装，直接用 Phinx 原生的 `$this->table()` 链式调用（见下节）。

**表级操作：直接用 `$this->table()`**

`Builder` 只负责字段与表选项的构建（含 `change()` 改列定义），`Migrator` 只保留 `builder()` / `create()` / `update()` / `save()` 四个对外入口。删列、重命名、索引、外键、删表等一律用 Phinx 原生链式写法，最后一次 `->save()` 提交：

| 操作         | 写法                                                                                                                                                               |
|------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| 删列         | `$this->table($this->name)->removeColumn('age')->removeColumn('score')->save();`                                                                                 |
| 改列名        | `$this->table($this->name)->renameColumn('old_name', 'new_name')->save();`                                                                                       |
| 表重命名       | `$this->table($this->name)->rename($this->new_name)->save();`                                                                                                    |
| 删表         | `$this->table($this->name)->drop()->save();`                                                                                                                     |
| 加普通 / 唯一索引 | `$this->table($this->name)->addIndex(['status', 'sort'], ['name' => 'idx_status_sort'])->addIndex('sku', ['name' => 'uk_sku', 'unique' => true])->save();`       |
| 删索引        | `$this->table($this->name)->removeIndex(['status', 'sort'])->removeIndexByName('uk_sku')->save();`                                                               |
| 加外键        | `$this->table($this->name)->addForeignKey('goods_id', 'goods', ['id'], ['delete' => 'CASCADE'])->save();`                                                        |
| 删外键        | `$this->table($this->name)->dropForeignKey('goods_id')->save();`                                                                                                 |
| 存在性判断      | `$this->hasTable($this->name)`；列 / 索引用 `$this->table($this->name)->hasColumn('age')` / `hasIndex([...])` / `hasIndexByName('...')` / `hasForeignKey('goods_id')` |

`$this->table()` 的第一个参数是**实际表名**，不是 `Builder`；需要表选项时才显式传第二个数组参数，或直接走 `create()` / `update()` / `save()`。删除前先判断存在，可以避免回滚重复执行时报错；完整示例见 [docs/README.md](docs/README.md)。

**模板对应关系**

- `CreateXxx` 类名 → `create.stub`：生成 `change()`，骨架是 `$this->create($builder, false)`（不加软删列）。
- 其他类名 → `update.stub`：生成 `up()` / `down()`，骨架是 `$this->update($builder)` 加一个空 `down()`，字段与回滚逻辑自行补齐。

`Builder::change()` 必须给出列名与新类型，无参调用会抛异常。完整迁移示例、字段预设、列类型原语、常见表结构与注意事项见 [docs/README.md](docs/README.md)。

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
