### 更新日志

#### v4.1.0

**API 变更**（现有 migration 需按此调整）

**版本支持**：PHP **`^8.0`**（PHP 7.4 用户请使用 v2.1.x，v2 线冻结维护），运行时仅要求 `ext-pdo`，零 composer 依赖

- **移除字段预设组**：删除 `src/preset/`（12 个分组 + `PresetGroup`）与 `Builder::__get()` / `GROUPS` 映射，本包回归纯数据库构建工具，不再携带业务字段约定；常用字段组合保留在 `docs/` 场景库，示例已全部改写为 Builder 原语写法（`$builder->user->username()` → `$builder->string('username', '登录账号', 30)->unique()`）。依赖预设的项目需把调用替换为对应原语，docs 各场景文件可对照复制
- 移除运行时整表预设（`TablePreset` 与 `$builder->table`）：表结构改由 `docs/` 场景库提供，复制场景里的 `change()` 到项目自己的迁移文件
- 移除 `Builder::createdAt()` / `updatedAt()` / `deletedAt()`：三个时间列仍由 `Migrator::create()` 自动注入，时间戳与软删分别由第二参 `$created_at`、第三参 `$soft_delete` 控制
- 方法重命名：`integerTiny/integerSmall/integerMedium` → `tinyInteger/smallInteger/mediumInteger`；`textTiny/textMedium/textLong` → `tinyText/mediumText/longText`；`invoice()` → `isInvoice()`；`ethnic()/ethnicStr()` → `ethnicId()/ethnicText()`
- `Builder::point()` 第二参改为 `$comment`，`$srid` 顺延为第三参
- `php migrate` 类名原样传入，不再自动补 `Create` 前缀或 `Table` 后缀；移除 `--column` 选项与 append / remove / rename 三个迁移模板，变更迁移统一使用空骨架
- 精简 API：`Builder` 移除 `rename()` / `remove()`（保留 `change()`），`Migrator` 移除 11 个表级操作薄封装、`invokeTable()` 改 `protected`；对外只留 `builder()` / `create()` / `update()` / `save()`
- 删列、重命名、索引、外键等统一用 Phinx 原生 `$this->table(...)->...->save()`；README / docs / 迁移示例的调用方式已同步
- `Builder` 移除 `addIndex()` / `addUniqueKey()` / `addForeignKey()` 及 `$index` / `$unique` / `$foreign`，`Migrator::parse()` 不再解析批量索引；组合索引、唯一索引、外键统一走 Phinx 原生 `$this->table()`。列级 `->index()` / `->unique()` 保留（仅单列）；`change()` 里的表级动作改用 `->update()` 提交
- 新增 `Builder::year()` / `json()` / `blob()` 三个字段原语，分别生成 YEAR、JSON、BLOB 列（JSON 需 MySQL 5.7.8+）
- 移除 `Builder::setVersion()` / `getVersion()` 与 `$version`：全仓无调用的版本探测，`precision` 不再依赖 MySQL 版本
- 新增 `Column::hasDefault()`：默认值改用独立布尔标记，`getDefault()` 未声明时返回 `null`（原哨兵 `-1` 移除）
- 移除 `Builder` 的 5 个空间集合类型方法（`geometryCollection()` / `multiPoint()` / `multiLineString()` / `multiPolygon()` / `multiPolygonZ()`）：上游 Phinx 未映射，调用必抛异常；替代写法见 `docs/README.md`

- 新增 `Column::precision()` / `scale()`：小数精度与位数，写入 Phinx 选项 `precision` / `scale`；`change()` 改 decimal 不给精度会生成裸 `DECIMAL`（MySQL 按 `DECIMAL(10,0)` 处理）

**修复**

- `Builder::addColumn()` 遇重复列名直接抛 `RuntimeException`，不再静默覆盖
- `Column::unique()` 不再把 string 列长度截到 191，`VARCHAR(255)` 加唯一索引时列定义保持不变
- 修复：`dateTime()` / `time()` / `timestamp()` 仅 `$precision > 0` 时写精度，默认不写括号以兼容 MySQL 5.5 / 5.6 全系；显式精度需 ≥ 5.6.4
- 修复：`->default(-1)` 不再被静默丢弃，`0` / `''` / `false` / `null` 等任意标量默认值均可声明
- 修复：`Builder` 新增 `registerColumn()` 统一登记与重复列名检查，`change()` 同名不再静默覆盖；`create()` 默认自动注入 `created_at` / `updated_at`，保留第二参 `$created_at` 开关，第三参 `$soft_delete` 单独控制 `deleted_at`
- 修复：`nullable()` 不再丢弃 default，`->nullable()->default(0)` 生成 `NULL DEFAULT 0`，两者相互独立
- 修复：`Migrator::exception()` 改抛 `RuntimeException`，交由 Phinx 命令层统一处理，不再 `echo` + `exit(1)`
- 修复：`PhinxConfig::config()` 改递归合并，`paths` / `templates` 等子键可部分覆盖，列表值整体替换
- 新增 `Schema` 类承载表级选项（引擎/注释/字符集/主键等），`Builder` 构造函数接收 `Schema` 实例/数组/字符串并暴露 `$builder->schema`，原 `setPrimaryKey` / `setEngine` / `setComment` / `setCollation` / `innoDB` / `myISAM` / `id` 从 `Builder` 移入 `Schema`，`getOptions()` 移除改用 `schema->toArray()`；引擎快捷方法更名为 `setEngineInnodb()` / `setEngineMyISAM()`，`id()` 为高频操作保留在 `Builder` 上并转发 schema

**其他**

- 移除 `magein/utils` 依赖
- 新增 `docs/` 场景库：17 个业务场景 + 常用片段，按 `account / content / trade / system / parts` 分组，中文索引可跳转，代码块已给出表注释
- `README.md` 回滚段补 `--target` / `--date` 语义速查与 `-x` dry-run，订正「回滚上一批」为「回滚最后一个迁移」
- 新增 PHPUnit 9.6 冒烟测试（`tests/`，57 例：列定义 SQL、nullable+default、配置递归合并、CLI 归一、模板表名推断、SQLite 内存库真实建表/索引/外键）与 GitHub Actions PHP 8.0 / 8.2 / 8.3 矩阵 CI
- `composer.json` 补 `require-dev` / `autoload-dev` / `scripts.test`，声明 `ext-pdo`，运行时零 composer 依赖，PHP 约束 **`^8.0`**；新增 `THIRD_PARTY.md` 说明提取代码来源与许可证
- 移除 `cakephp/database` / `psr/container` 依赖：本包迁移链路走 PDO 直连不触发加载，保持零运行时依赖（`composer.json` 仅声明 `php` / `ext-pdo`）
- 迁移示例补 3 个：`up()` 侧删字段 + 删索引（含 `hasColumn()` / `hasIndexByName()` 守卫与对应 `down()` 还原）、`renameColumn()` 重命名列、`execute()` 原生 DDL 切换表引擎
- 修复示例 `alter_goods_price`：`change()` 是整列重建，只写类型与注释会清掉默认值、精度，up 补 `->default(0)->precision(10)->scale(2)`，down 补 `->default(0)` 并把注释还原为原定义「价格 单位分」

#### v4.0.0

1. 支持php:^8.0