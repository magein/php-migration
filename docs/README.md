# 字段类型与表结构场景

字段预设组、Builder 列类型原语、索引说明、常见表结构示例与迁移注意事项统一放在这里；命令行与迁移文件的基本用法见 [根目录 README](../README.md)。

## 内置字段

### 常规字段

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

### 其他字段类型

构建器另提供三个原语，返回值同样是 `Column`，可继续链式调用 `nullable()`、`comment()`、`length()` 等：

```php
$builder->year('graduation_year', '毕业年份');  // YEAR(4)
$builder->json('extra', '扩展数据');             // JSON，需 MySQL 5.7.8+
$builder->blob('payload', '二进制内容');         // BLOB
```

`year()` 由 Phinx 固定生成 `YEAR(4)`；`json()` 依赖 MySQL 5.7.8 起的原生 JSON 类型；`blob()` 默认生成 BLOB，需要更大容量时链式 `->length()` 指定（配合 `Phinx\Db\Adapter\MysqlAdapter::BLOB_*` 常量）。

### 唯一索引与列长度

`->unique()` 只登记唯一索引，不改动列长度（旧版本会把 string 列截到 191）。191 来自早期默认行格式：`COMPACT` / `REDUNDANT` 下 InnoDB 索引前缀上限 767 字节，`utf8mb4` 每字符最多 4 字节，767 ÷ 4 ≈ 191；`utf8`（mb3）下 767 ÷ 3 ≈ 255，本来就够。

MySQL 5.7.7 起 `innodb_file_format` 默认 Barracuda、`innodb_large_prefix` 默认 ON，5.7 的 `innodb_default_row_format` 默认 `DYNAMIC`，`DYNAMIC` / `COMPRESSED` 的索引前缀上限是 3072 字节；`utf8mb4` 的 `VARCHAR(255)` 占 1020 字节，可以直接建唯一索引。8.0 起这是默认行为。

前提是 InnoDB + `DYNAMIC`。本包默认引擎是 MyISAM（可用 `$builder->innoDB()` 切换），若显式使用 `COMPACT` / `REDUNDANT` 或仍是旧配置，可能遇到 `ERROR 1071 Specified key was too long`；这种情况自行 `->length(191)` 或调整表选项，构建器不会替你改列定义。

## 常见表结构场景

按业务场景整理的可复制表结构代码。**点场景名 → 打开对应文档 → 复制 `change()` 里的代码**，粘到自己的 migration 中按项目需要增删改列。

这里全是**示例，不是运行时 API**。包不预设整张表——表结构属于每个项目自己的领域模型，本包只提供字段预设（`src/preset/fields/`）和 Builder 原语；迁移文件应当自包含，读一个文件就能知道当时建了什么。

几点约定：

- `$builder = $this->builder('用户表');` 和 `$this->create($builder);` 已经写在每段代码里，复制整块即可；`protected $name` 改成文档里给的**表名**。
- `builder()` 的字符串参数是**表注释**：`Migrator::builder()` 原样传给 `Builder::__construct()`，构造函数写入 `options['comment']`（`src/Builder.php:98`），建表时由 `Migrator::invokeTable()` 使用。每个场景都填好了对应的中文注释，不要传空串。
- `id` 需要手写；`created_at / updated_at / deleted_at` 由 `Migrator::create()` 自动注入，不用重复写。不需要软删就传 `$this->create($builder, false);`。
- `$builder->user->xxx()` 这类是字段预设组，`$builder->string()` 这类是 Builder 原语；两者可以混用。金额统一「单位分」，`integer()` 的第三参是 `signed`，不要当默认值用。

目录按业务分组：`account/` 用户与账号、`content/` 内容与展示、`trade/` 商品与交易、`system/` 系统与附件、`parts/` 常用片段。

### 用户与账号

| 场景 | 说明 | 表名 |
| --- | --- | --- |
| [用户表](account/user.md) | 账号基础信息：登录名、密码、昵称、头像、手机邮箱 | `user` |
| [用户收货地址](account/user-address.md) | 省市区、详细地址、邮编、经纬度、默认地址 | `user_address` |
| [员工表](account/employee.md) | 人员档案：证件正反面、籍贯民族、居住地址 | `employee` |
| [商家 / 店铺表](account/shop.md) | 平台店铺主体：归属用户、图片、联系人、地区 | `shop` |
| [企业表](account/business.md) | 企业主体：名称简称、企业代码、行业规模、联系人 | `business` |
| [银行字典表](account/bank.md) | 支付 / 提现场景的银行字典 | `bank` |

### 内容与展示

| 场景 | 说明 | 表名 |
| --- | --- | --- |
| [分类表](content/category.md) | 通用树形分类，带状态与排序 | `category` |
| [文章表](content/article.md) | 内容发布：正文、缩略图、推荐标记、互动计数 | `article` |
| [轮播图表](content/carousel.md) | 图片、跳转、生效时间、投放场景 | `carousel` |
| [行政区划表](content/region.md) | 省市区字典，code + 拼音 + 层级 | `region` |
| [行业字典表](content/industry.md) | 树形行业分类 | `industry` |

### 商品与交易

| 场景 | 说明 | 表名 |
| --- | --- | --- |
| [商品表](trade/goods.md) | 货号、封面、简介详情、三种价格、库存销量、规格 | `goods` |
| [订单表](trade/order.md) | 交易主表：买卖双方、收货地址、金额、状态时间线 | `order` |
| [支付订单表](trade/pay-order.md) | 支付流水、第三方交易号、回调地址与结果 | `pay_order` |
| [优惠券规则表](trade/coupon-rule.md) | 类型、生效时间、抵扣与门槛金额 | `coupon_rule` |
| [发票表](trade/invoice.md) | 发票类型、抬头、纳税人识别号、开票状态 | `invoice` |

### 系统与附件

| 场景 | 说明 | 表名 |
| --- | --- | --- |
| [附件 / 文件表](system/file.md) | 上传文件记录：路径、大小、后缀、mime、hash | `file` |

### 不成表的片段

[常用片段](parts/parts.md)：省市区、省市区+详细地址、状态+排序、生效起止时间——复制到任意表里用。

## 注意事项

- `create()` 自动注入 `created_at` / `updated_at` / `deleted_at`，前两列非空无默认，手写 INSERT 必须给值。
- 外键要求两张表都是 InnoDB、外键列与主键列类型和符号一致；Phinx 自动主键是 unsigned，外键列要用 `unsignedInteger()`。
- 列级 `->index()` / `->unique()` 只支持单列；批量组合索引用 `$builder->addIndex()`。
- `Builder::remove()` 在 `down()` 方向抛 `RuntimeException`，删列必须写显式 `up()` / `down()`。
