### 更新日志

#### v2.1.0

**API 变更**（现有 migration 需按此调整）

- 字段预设重组：去掉 MF 前缀，统一 `XxxPreset` 命名并迁入 `src/preset/fields/`；最终 12 个组 `user / basic / addr / file / image / article / goods / spec / date / contact / flag / order`，其中 `is` 更名 `flag`
- 移除运行时整表预设（`TablePreset` 与 `$builder->table`）：表结构改由 `docs/` 场景库提供，复制场景里的 `change()` 到项目自己的迁移文件
- 移除 `Builder::createdAt()` / `updatedAt()` / `deletedAt()`：三个时间列仍由 `Migrator::create()` 自动注入，软删开关行为不变
- 方法重命名：`integerTiny/integerSmall/integerMedium` → `tinyInteger/smallInteger/mediumInteger`；`textTiny/textMedium/textLong` → `tinyText/mediumText/longText`；`invoice()` → `isInvoice()`；`ethnic()/ethnicStr()` → `ethnicId()/ethnicText()`
- `Builder::point()` 第二参改为 `$comment`，`$srid` 顺延为第三参
- `php migrate` 类名原样传入，不再自动补 `Create` 前缀或 `Table` 后缀；移除 `--column` 选项与 append / remove / rename 三个迁移模板，变更迁移统一使用空骨架
- `Builder::rename()` / `remove()` 不再返回 `Column`；参数为空或新旧同名时抛异常，`down()` 中使用 `remove()` 抛 `RuntimeException`
- 新增 `Builder::year()` / `json()` / `blob()` 三个字段原语，分别生成 YEAR、JSON、BLOB 列（JSON 需 MySQL 5.7.8+）

**修复**

- 修正 9 处预设方法列名写错导致的同名覆盖（原本会丢列）
- `Builder::addColumn()` 遇重复列名直接抛 `RuntimeException`，不再静默覆盖
- `FilePreset::ext()` 由 integer 改 string、`filesize()` 改 bigInteger
- `OrderPreset::couponId()` 去掉 nullable 使 `default(0)` 生效；`refundedAt()` 统一为 dateTime
- `AddressPreset::lng()/lat()` 改 `decimal(10,6)`，`location()` 仍为 string
- `Column::unique()` 不再把 string 列长度截到 191，`VARCHAR(255)` 加唯一索引时列定义保持不变

**其他**

- `Builder::__get` 改显式映射表，未知预设组抛 `InvalidArgumentException`；移除 `magein/utils` 依赖
- 新增 `docs/` 场景库：17 个业务场景 + 常用片段，按 `account / content / trade / system / parts` 分组，中文索引可跳转，代码块已给出表注释

#### v2.0.1

1. 修改comment_num字段的comment错误(点赞数量->评论错误)
2. 修改composer.json中homepage的指向错误

#### v2.0.0

1. 支持php:^7.1
2. 将常规字段从构建器中剔除,防止混淆
3. 优化相关模版