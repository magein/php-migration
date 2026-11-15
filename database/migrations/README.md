# 迁移与种子数据

`database/` 整体纳入 git，作为包的回归基线：改动 `Builder` / `Migrator` 后，从空库按文件名顺序跑一遍 `migrations/`，再跑一次 `seeds/`，即可确认列类型、变更操作与数据填充都正常。

```shell
php migrate run          # 执行全部迁移
php migrate rollback     # 回滚
php migrate seed:run     # 执行全部 seed
```

## migrations（11 个）

| 文件                             | 表                | 覆盖点                                                       |
|--------------------------------|------------------|-------------------------------------------------------------|
| 20250701000001_create_all_types | `all_types`      | Builder 全部 40 个列类型原语（含 year / json / blob / 空间类型）        |
| 20250801000001_create_user     | `user`           | `change()` 可逆、预设组 + 原语、软删列自动注入、`innoDB()`            |
| 20250801000012_create_goods    | `goods`          | 价格 / 库存预设、组合索引、InnoDB                                   |
| 20250801000018_create_goods_category | `goods_category` | 纯原语、联合唯一约束、`unsignedInteger()` 对齐主键符号              |
| 20250802000001_append_user_profile | `user`        | `update()` 加列、`->after()` 定位、`down()` 删回                   |
| 20250802000002_alter_goods_price | `goods`         | `Builder::change()` → `changeColumn()`                        |
| 20250802000006_add_indexes     | `goods`          | `addIndex()` / `addUniqueIndex()`，按列与按名删除                  |
| 20250802000007_add_foreign_key | `goods_category` | `addForeignKey()` / `dropForeignKey()`                       |
| 20250802000010_save_idempotent | `demo_config`    | `save()` 幂等建表                                             |
| 20250802000012_change_table_options | `goods`      | `changeComment()->save()` 改表注释                             |
| 20250811073741_user_append     | `user`           | 历史示例，时间戳最后，追加积分等列                                     |

## seeds（1 个）

| 文件             | 说明                                              |
|----------------|-------------------------------------------------|
| `UserSeeder.php` | 往 `user` 写 2 条基础账号；先按 username 删除再插入，可重复执行 |

seed 文件名必须是纯字母数字（Phinx `Util::isValidSeedFileName()` 的 `/^([a-z][a-z\d]*)\.php$/i`），类名与文件名一致；不带时间戳前缀，按文件名排序执行。

## 注意事项

- `create()` 自动注入 `created_at` / `updated_at` / `deleted_at`，前两列非空无默认，手写 INSERT 必须给值。
- 外键要求两张表都是 InnoDB、外键列与主键列类型和符号一致；Phinx 自动主键是 unsigned，外键列要用 `unsignedInteger()`。
- 列级 `->index()` / `->unique()` 只支持单列；批量组合索引用 `$builder->addIndex()`。
- `Builder::remove()` 在 `down()` 方向抛 `RuntimeException`，删列必须写显式 `up()` / `down()`。
