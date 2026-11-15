# Builder 参考与表结构场景

Builder 的表选项配置、支持的字段类型、预设分组、迁移文件写法与常见表结构场景统一放在这里。命令行与迁移文件的基本用法见 [根目录 README](../README.md)，`phinx.php` 配置见 [config.md](config.md)。

## 表选项配置

`$builder = $this->builder()` 的入参就是表配置：传**字符串**当表注释，传**数组**当表选项。

```php
// 最常用：只写表注释
$builder = $this->builder('用户表');

// 完整表选项
$builder = $this->builder([
    'comment'     => '用户表',
    'engine'      => 'InnoDB',
    'collation'   => 'utf8mb4_unicode_ci',
    'primary_key' => ['user_id', 'tenant_id'],
    'id'          => false,
]);

// 也可以建好之后再逐个设置
$builder->setComment('用户表');
$builder->setEngine('InnoDB');
$builder->setCollation('utf8mb4_unicode_ci');
$builder->setPrimaryKey('id');        // 传 null 则关闭主键
$builder->innoDB();                   // 快捷切到 InnoDB
$builder->myISAM();                   // 快捷切到 MyISAM
$builder->id();                       // 让 Phinx 自动建自增主键 id
```

数组只透传下面这些键（Phinx 能识别的表选项），其余键静默丢弃：

| 键 | 说明 | 默认值 |
| --- | --- | --- |
| `id` | 是否自动创建自增主键 | `false` |
| `primary_key` | 主键列名或列名数组 | 无 |
| `comment` | 表注释 | 无 |
| `engine` | 表引擎 | `InnoDB` |
| `collation` | 字符集排序规则 | `utf8_unicode_ci` |
| `row_format` | 行记录格式 | 无 |
| `signed` | 自增主键是否有符号 | 无 |
| `limit` | 自增主键列的 limit | 无 |

不传 `id` 时默认关掉自增主键；Phinx 在缺少该选项时会自动补一个，所以「不要主键」必须显式写 `false` 或 `null`。

## 支持的字段类型

以下都是 `Builder` 的列原语，返回值是 `Column`，可继续链式调用 `nullable()`、`default()`、`comment()`、`length()`、`after()`、`index()`、`unique()`。第一参统一是列名，第二参是注释。

### 主键与枚举

| 方法 | 生成类型 | 说明 |
| --- | --- | --- |
| `id()` | — | 写入表选项 `id = true`，由 Phinx 建自增主键 |
| `uuid($col, $comment)` | UUID | 定长字符串，适合分布式主键 |
| `enum($col, $values)` | ENUM | 第二参是枚举值数组 |

### 整数

基础方法签名都是 `($col, $comment = null, $signed = true, $increment = false)`，靠 `limit` 区分宽度：

| 方法 | 生成类型 |
| --- | --- |
| `integer()` | INT |
| `tinyInteger()` | TINYINT |
| `smallInteger()` | SMALLINT |
| `mediumInteger()` | MEDIUMINT |
| `bigInteger()` | BIGINT |

配套的无符号与自增快捷方法，签名是 `($col, $comment = null, $increment = false)`：

| 系列 | 方法 |
| --- | --- |
| 无符号 | `unsignedTinyInteger()` `unsignedSmallInteger()` `unsignedMediumInteger()` `unsignedInteger()` `unsignedBigInteger()` |
| 自增 | `tinyIncrements()` `smallIncrements()` `mediumIncrements()` `integerIncrements()` `bigIncrements()` |

`*Increments()` 默认列名为 `id`，内部走对应的 `unsigned*()` 并打开 `increment`。外键列要用 `unsignedInteger()`：Phinx 自动主键是 unsigned，符号不一致会建不上外键。

### 字符串与文本

| 方法 | 生成类型 | 说明 |
| --- | --- | --- |
| `string($col, $comment, $length = 255)` | VARCHAR | 默认长度 255 |
| `char($col, $comment, $length = 255)` | CHAR | 默认长度 255 |
| `text()` | TEXT |  |
| `tinyText()` / `mediumText()` / `longText()` | TINYTEXT / MEDIUMTEXT / LONGTEXT | 用 `limit` 区分 |

### 二进制与扩展类型

| 方法 | 生成类型 | 说明 |
| --- | --- | --- |
| `json($col, $comment)` | JSON | 需 MySQL 5.7.8+ 的原生 JSON |
| `blob($col, $comment)` | BLOB | 需要更大容量时链式 `->length()`，配合 `MysqlAdapter::BLOB_*` 常量 |
| `year($col, $comment)` | YEAR(4) | Phinx 固定生成 4 位年份 |

### 小数

| 方法 | 生成类型 | 默认参数 |
| --- | --- | --- |
| `float($col, $comment, $precision = 8, $scale = 2, $signed = true)` | FLOAT | 8, 2 |
| `double($col, $comment, $precision = null, $scale = null, $signed = true)` | DOUBLE | 由 MySQL 决定 |
| `decimal($col, $comment, $precision = 10, $scale = 2, $signed = true)` | DECIMAL | 10, 2 |
| `floatUnsigned()` / `doubleUnsigned()` / `decimalUnsigned()` | 同上 | 传 `signed = false` |

Phinx 用 `!isSigned()` 生成 UNSIGNED，所以只有 `*Unsigned()` 系列是无符号，基础的三个默认有符号。经纬度、金额可能为负的场景别用错。

### 布尔与日期时间

| 方法 | 生成类型 | 说明 |
| --- | --- | --- |
| `boolean()` | BOOLEAN |  |
| `date()` | DATE |  |
| `dateTime($col, $comment, $precision = 0, $null = false)` | DATETIME | `$precision > 0` 时生成 `DATETIME(n)` |
| `time($col, $comment, $precision = 0)` | TIME | `$precision > 0` 时生成 `TIME(n)` |
| `timestamp($col, $comment, $precision = 0)` | TIMESTAMP | `$precision > 0` 时生成 `TIMESTAMP(n)` |

小数秒精度 `$precision` 取 0-6，只有 > 0 才写入列定义；不传或传 0 生成不带括号的类型，兼容 MySQL 5.5 / 5.6 全系。显式指定精度需要 MySQL ≥ 5.6.4 —— 低版本不支持 `DATETIME(n)` 语法（报 `ERROR 1064`），且会丢弃小数秒；要跨版本保留微秒请改用 `BIGINT` 存毫秒时间戳。Builder 不探测数据库版本，也不做降级。

### 空间类型

| 方法 | 生成类型 |
| --- | --- |
| `geometry()` `point($col, $comment, $srid = null)` `lineString()` `polygon()` | GEOMETRY / POINT / LINESTRING / POLYGON |

`point()` 的 `$srid` 只在 MySQL 8.0+ 生效，不传则不写入该选项；空间类型建议 MySQL 5.7+。

**5 个集合类型方法已移除（2026-11-15）**：`geometryCollection()` / `multiPoint()` / `multiLineString()` / `multiPolygon()` / `multiPolygonZ()` 原先只是把类型串交给 Phinx，而上游 `MysqlAdapter::getSqlType()` 只映射上面 4 种空间类型，`Table::addColumn()` 在 `isValidColumnType()` 阶段就会拒绝，调用必定抛 `UnsupportedColumnTypeException`（与 MySQL 版本无关）；`MULTIPOLYGONZ` 也不是 MySQL 的列类型。它们是调不通的空壳，已从 `Builder` 删除，不再算支持的字段类型。

确实需要 `MULTIPOINT` / `MULTILINESTRING` / `MULTIPOLYGON` / `GEOMETRYCOLLECTION` 时，用 Phinx 原生的自定义类型（MySQL 5.7+）：

```php
// up() / down() 中；change() 内禁用 ->save()
$this->table('t')->addColumn('col', \Phinx\Util\Literal::from('MULTIPOINT'))->save();
```

### 唯一索引与列长度

`->unique()` 只登记唯一索引，不改动列长度（旧版本会把 string 列截到 191）。191 来自早期默认行格式：`COMPACT` / `REDUNDANT` 下 InnoDB 索引前缀上限 767 字节，`utf8mb4` 每字符最多 4 字节，767 ÷ 4 ≈ 191；`utf8`（mb3）下 767 ÷ 3 ≈ 255，本来就够。

MySQL 5.7.7 起 `innodb_file_format` 默认 Barracuda、`innodb_large_prefix` 默认 ON，5.7 的 `innodb_default_row_format` 默认 `DYNAMIC`，`DYNAMIC` / `COMPRESSED` 的索引前缀上限是 3072 字节；`utf8mb4` 的 `VARCHAR(255)` 占 1020 字节，可以直接建唯一索引。8.0 起这是默认行为。

前提是 InnoDB + `DYNAMIC`。本包默认引擎是 InnoDB（要 MyISAM 用 `$builder->myISAM()`），若显式使用 `COMPACT` / `REDUNDANT` 或仍是旧配置，可能遇到 `ERROR 1071 Specified key was too long`；这种情况自行 `->length(191)` 或调整表选项，构建器不会替你改列定义。

## 预设字段分组

`$builder->user->xxx()` 这类是字段预设组，用 `__get()` 按 `GROUPS` 映射实例化，未知分组会抛 `InvalidArgumentException`。每组方法内部调用上面的列原语并预填列名、类型与注释，返回值同样是 `Column`，可以继续链式设置。下划线列名由驼峰方法名转换，如 `userId()` → `user_id`、`marketPrice()` → `market_price`。

| 分组 | 预设类 | 可用字段方法 |
| --- | --- | --- |
| `$builder->user` | `UserPreset` | `username()` `nickname()` `avatar()` `account()` `password()` `age()` `birthday()` `gender()` `points()` `score()` `idType()` `idNumber()` `idFront()` `idBack()` `nativePlace()` `ethnicId()` `ethnicText()` `userId()` `memberId()` |
| `$builder->basic` | `BasicPreset` | `name()` `remark()` `status()` `sort()` `type()` `alias()` `reason()` |
| `$builder->addr` | `AddressPreset` | `postcode()` `province()` `city()` `district()` `street()` `area()` `address()` `location()` `lng()` `lat()` |
| `$builder->file` | `FilePreset` | `filename()` `originName()` `filepath()` `filesize()` `ext()` `mime()` `hash()` `md5()` |
| `$builder->image` | `ImagePreset` | `thumb()` `logo()` `icon()` `image()` `picture()` `photo()` `cover()` |
| `$builder->article` | `ArticlePreset` | `title()` `description()` `intro()` `author()` `content()` `tag()` |
| `$builder->goods` | `GoodsPreset` | `price()` `num()` `stock()` `attribute()` `sku()` `sales()` `marketPrice()` `costPrice()` |
| `$builder->spec` | `SpecPreset` | `size()` `width()` `height()` `weight()` `unit()` |
| `$builder->date` | `DatePreset` | `beginTime()` `startTime()` `endTime()` `auditedAt()` `expiredAt()` |
| `$builder->contact` | `ContactPreset` | `phone()` `mobile()` `email()` `tel()` `contact()` |
| `$builder->flag` | `FlagPreset` | `isHot()` `isRecommend()` `isPublished()` `isOriginal()` `isUp()` `isForbid()` `isShow()` `isCollection()` `isSend()` |
| `$builder->order` | `OrderPreset` | `payNo()` `orderNo()` `refundNo()` `tradeNo()` `orderAmount()` `goodsAmount()` `discountAmount()` `actualAmount()` `freightAmount()` `refundAmount()` `refundedAt()` `orderStatus()` `payStatus()` `paidAt()` `payMethod()` `buyerId()` `buyerName()` `buyerPhone()` `receiverName()` `receiverPhone()` `couponId()` `couponAmount()` `deliveryCompany()` `deliveryNo()` `deliveryTime()` `receivedAt()` `finishedAt()` `isInvoice()` |

预设组与列原语可以混用，同一列名重复声明会抛「重复的列名」。

### 常规字段示例

```php
$builder = $this->builder('用户表');
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

## 迁移文件推荐写法

变更模板只给骨架，`protected $name`、`up()` 里的字段定义和 `down()` 都要按实际迁移手写；`down()` 尤其不能省，删列、改列、重命名、删表都无法自动反转。

> **提交方法：`create()` / `update()` / `save()` 不能混用**（`migrate.stub` 模板内的提醒）
> 想让 Phinx 自动升级 / 降级，就把逻辑写在 `change()` 里，最终只调用 `create()` 和 `update()`，**不要调用 `save()`**；创建使用 `create()`、更新使用 `update()`。
> `save()` 靠 `hasTable()` 运行时判断建表还是改表，升级与回滚两次判断结果不同，反转会出错；删列、重命名、索引、外键等依赖 `->save()` 提交的表级操作同理，都不要放进 `change()`。
> 无法自动反转的操作改用 `up()` / `down()` 分开写 —— **下面三个示例都是这种写法**，其中出现的 `->save()` 是提交动作的必要一环，与 `change()` 里禁用 `$this->save($builder)` 并不冲突。
> 表级操作不再有 `Migrator` 封装，统一走 Phinx 原生 `$this->table($this->name)->...->save()`；`Builder` 只负责字段与表选项构建，`Builder::change()` 用于改已有列定义。
>
> 三个方法的完整区别与表级操作速查见 [根目录 README](../README.md) 的「迁移文件」小节。

字段在 `up()` 里用 `$builder` 声明。`Builder::change()` 必须给出列名与新类型，无参调用会抛异常：

```php
$builder->change('username', 'string', '登录账号');   // 列名 + 新类型 + 注释
```

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
        $this->table($this->name)
            ->removeColumn('age')
            ->removeColumn('wechat_openid')
            ->save();
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
        $this->table($this->name)->removeColumn('legacy_flag')->save();
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

**表级操作统一写法**

`Builder` 只构建字段与表选项，`Migrator` 只保留 `builder()` / `create()` / `update()` / `save()`；删列、重命名、索引、外键、删表一律用 Phinx 原生 `$this->table()`，最后 `->save()` 提交，同一个表对象可以攒多个动作一次提交：

```php
$table = $this->table($this->name);

if ($table->hasIndex(['status', 'sort'])) {
    $table->removeIndex(['status', 'sort']);
}
if ($table->hasForeignKey('goods_id')) {
    $table->dropForeignKey('goods_id');
}

$table->save();
```

常用动作（`removeColumn()` / `renameColumn()` / `rename()` / `drop()` / `addIndex()` / `removeIndexByName()` / `addForeignKey()` / `dropForeignKey()`）与存在性判断的完整速查表见 [根目录 README](../README.md) 的「表级操作」小节。删除前先判断存在，避免回滚时重复执行报错。

## 常见表结构场景

按行业 / 业务场景整理的可复制字段表，作为建表参考。**点场景名 → 打开对应文档 → 复制 `change()` 里的代码**，粘到自己的 migration 中按项目需要增删改列。

这里全是**示例，不是运行时 API**。包不预设整张表——表结构属于每个项目自己的领域模型，本包只提供字段预设（`src/preset/fields/`）和 Builder 原语；迁移文件应当自包含，读一个文件就能知道当时建了什么。

几点约定：

- `$builder = $this->builder('用户表');` 和 `$this->create($builder);` 已经写在每段代码里，复制整块即可；`protected $name` 改成文档里给的**表名**。
- `builder()` 的字符串参数是**表注释**：`Migrator::builder()` 原样传给 `Builder::__construct()`，构造函数写入 `options['comment']`，建表时由 `Migrator` 内部读取。每个场景都填好了对应的中文注释，不要传空串。
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
- 删列、改列名、表重命名、索引 / 外键、删表等表级操作统一走 Phinx 原生 `$this->table()` 链式调用，并且必须显式写 `up()` / `down()`；`Builder` 只构建字段与表选项，`change()` 是它唯一的「改已有列定义」入口。
