### 更新日志

#### v2.1.0

**API 变更**（现有 migration 需按此调整）

- 字段预设重组：去掉 MF 前缀，统一 `XxxPreset` 命名并迁入 `src/preset/fields/`；最终 12 个组 `user / basic / addr / file / image / article / goods / spec / date / contact / flag / order`，其中 `is` 更名 `flag`
- 移除运行时整表预设（`TablePreset` 与 `$builder->table`）：表结构改由 `docs/` 场景库提供，复制场景里的 `change()` 到项目自己的迁移文件
- 移除 `Builder::createdAt()` / `updatedAt()` / `deletedAt()`：三个时间列仍由 `Migrator::create()` 自动注入，软删开关行为不变
- 方法重命名：`integerTiny/integerSmall/integerMedium` → `tinyInteger/smallInteger/mediumInteger`；`textTiny/textMedium/textLong` → `tinyText/mediumText/longText`；`invoice()` → `isInvoice()`；`ethnic()/ethnicStr()` → `ethnicId()/ethnicText()`
- `Builder::point()` 第二参改为 `$comment`，`$srid` 顺延为第三参
- `php migrate` 类名原样传入，不再自动补 `Create` 前缀或 `Table` 后缀；移除 `--column` 选项与 append / remove / rename 三个迁移模板，变更迁移统一使用空骨架
- 精简 API：`Builder` 移除 `rename()` / `remove()`（保留 `change()`），`Migrator` 移除 11 个表级操作薄封装、`invokeTable()` 改 `protected`；对外只留 `builder()` / `create()` / `update()` / `save()`
- 删列、重命名、索引、外键等统一用 Phinx 原生 `$this->table(...)->...->save()`；README / docs / 迁移示例的调用方式已同步
- 新增 `Builder::year()` / `json()` / `blob()` 三个字段原语，分别生成 YEAR、JSON、BLOB 列（JSON 需 MySQL 5.7.8+）
- 移除 `Builder::setVersion()` / `getVersion()` 与 `$version`：全仓无调用的版本探测，`precision` 不再依赖 MySQL 版本
- 新增 `Column::hasDefault()`：默认值改用独立布尔标记，`getDefault()` 未声明时返回 `null`（原哨兵 `-1` 移除）
- 移除 `Builder` 的 5 个空间集合类型方法（`geometryCollection()` / `multiPoint()` / `multiLineString()` / `multiPolygon()` / `multiPolygonZ()`）：上游 Phinx 未映射，调用必抛异常；替代写法见 `docs/README.md`

**修复**

- 修正 9 处预设方法列名写错导致的同名覆盖（原本会丢列）
- `Builder::addColumn()` 遇重复列名直接抛 `RuntimeException`，不再静默覆盖
- `FilePreset::ext()` 由 integer 改 string、`filesize()` 改 bigInteger
- `OrderPreset::couponId()` 去掉 nullable 使 `default(0)` 生效；`refundedAt()` 统一为 dateTime
- `AddressPreset::lng()/lat()` 改 `decimal(10,6)`，`location()` 仍为 string
- `Column::unique()` 不再把 string 列长度截到 191，`VARCHAR(255)` 加唯一索引时列定义保持不变
- 修复：`Migrator::parse()` 批量唯一索引与普通索引改为两个独立 `if`，不再因 `elseif` 静默丢弃普通索引
- 修复：`dateTime()` / `time()` / `timestamp()` 仅 `$precision > 0` 时写精度，默认不写括号以兼容 MySQL 5.5 / 5.6 全系；显式精度需 ≥ 5.6.4
- 修复：`->default(-1)` 不再被静默丢弃，`0` / `''` / `false` / `null` 等任意标量默认值均可声明
- 修复：`Builder` 新增 `registerColumn()` 统一登记与重复列名检查，`change()` 同名不再静默覆盖；`create()` 自动注入时间戳为既有设计，不加开关
- 修复：`nullable()` 不再丢弃 default，`->nullable()->default(0)` 生成 `NULL DEFAULT 0`，两者相互独立
- 修复：`Migrator::exception()` 改抛 `RuntimeException`，交由 Phinx 命令层统一处理，不再 `echo` + `exit(1)`
- 修复：`PhinxConfig::config()` 改递归合并，`paths` / `templates` 等子键可部分覆盖，列表值整体替换
- 新增 `Schema` 类承载表级选项（引擎/注释/字符集/主键等），`Builder` 构造函数接收 `Schema` 实例/数组/字符串并暴露 `$builder->schema`，原 `setPrimaryKey` / `setEngine` / `setComment` / `setCollation` / `innoDB` / `myISAM` / `id` 从 `Builder` 移入 `Schema`，`getOptions()` 移除改用 `schema->toArray()`；引擎快捷方法更名为 `setEngineInnodb()` / `setEngineMyISAM()`，`id()` 为高频操作保留在 `Builder` 上并转发 schema

**其他**

- `Builder::__get` 改显式映射表，未知预设组抛 `InvalidArgumentException`；移除 `magein/utils` 依赖
- 新增 `docs/` 场景库：17 个业务场景 + 常用片段，按 `account / content / trade / system / parts` 分组，中文索引可跳转，代码块已给出表注释
- `README.md` 回滚段补 `--target` / `--date` 语义速查与 `-x` dry-run，订正「回滚上一批」为「回滚最后一个迁移」
- 新增 PHPUnit 9.6 冒烟测试（`tests/`，46 例：列定义 SQL、预设组唯一性、nullable+default、配置递归合并、CLI 归一、模板表名推断、SQLite 内存库真实建表/索引/外键）与 GitHub Actions PHP 7.4 / 8.0 / 8.2 矩阵 CI
- `composer.json` 补 `require-dev` / `autoload-dev` / `scripts.test`，声明 `ext-pdo`，运行时零 composer 依赖，PHP 下限 `^7.4`；新增 `THIRD_PARTY.md` 说明提取代码来源与许可证
- 移除 `cakephp/database` / `psr/container` 依赖：本包迁移链路走 PDO 直连不触发加载，保持零运行时依赖（`composer.json` 仅声明 `php` / `ext-pdo`）

#### v2.0.1

1. 修改comment_num字段的comment错误(点赞数量->评论错误)
2. 修改composer.json中homepage的指向错误

#### v2.0.0

1. 支持php:^7.1
2. 将常规字段从构建器中剔除,防止混淆
3. 优化相关模版