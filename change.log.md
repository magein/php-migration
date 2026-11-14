### 更新日志

#### v2.1.0

本次为较大变更(涉及公开类名、目录结构、字段行为),以下条目同属一个未提交批次,合并发布为一个版本。

1. 修正 9 处列名写错导致的字段互相覆盖
    - CommonPreset(原 MFGen):tel() contact -> tel、height() width -> height、weight() width -> weight、redirect() url -> redirect、ip() reason -> ip
    - UserPreset(原 MFUser):idFront() id_number -> id_front、idBack() id_number -> id_back、nativePlace() id_number -> native_place、ethnicStr() ethnic -> ethnic_str
2. 修复后 business() 的 contact/tel、employee() 的 native_place/id_number 不再互相覆盖,可正常建列
3. 字段预设重构:去掉 MF 前缀,统一 XxxPreset 命名,目录迁移到 src/preset/
    - MFBase -> preset/PresetGroup.php (abstract)
    - MFGen -> preset/fields/CommonPreset.php
    - MFUser -> preset/fields/UserPreset.php
    - MFAddr -> preset/fields/AddressPreset.php
    - MFFile -> preset/fields/FilePreset.php
    - MFIs -> preset/fields/FlagPreset.php
    - MFOrder -> preset/fields/OrderPreset.php
    - MFComb -> preset/tables/TablePreset.php
4. Builder::__get 由字符串拼类名改为显式映射表,未知预设组抛 InvalidArgumentException,不再依赖 magein/utils 的 Variable::pascal
5. 继承边界:FlagPreset 不再继承 CommonPreset,TablePreset 继承 PresetGroup 并去掉重复的 builder 属性与构造函数
6. TablePreset::userAddress() 去掉多余的 new MFAddr 中转,统一使用 $this->builder
7. 预设组属性名同步更名:gen -> common、is -> flag、comb -> table;user/addr/file/order 保持原样,类名与 namespace 不变
8. 新增 issues.md 记录遗留问题,定位改为 类名::方法() 并同步更新
9. 同步更新 README、database/migrations 下 10 个迁移文件及 TablePreset 内部调用;仅替换 builder 的 is 属性访问,未触碰 Column::isRemove()/isRename()/isChange()
10. 联系方式字段收口:phone()/mobile() 保留在 CommonPreset(两个不同列名,按使用习惯选用),email() 从 UserPreset 移入 CommonPreset;删除 UserPreset 中三处重复定义,TablePreset/README 调用改走 common 组
11. 新增 ImagePreset 归纳图片字段:thumb/logo/icon/image/picture 五个方法从 CommonPreset 搬入,Builder 挂载 image 属性组;TablePreset 的 article()/bank() 改走新组;icon() 去掉 60 的长度限制,统一为默认 255(云端/签名路径可能超长),其余定义不变
12. ImagePreset 补充 photo()(照片)、cover()(封面图),与 image()/picture() 同结构;图片字段组到此收口,不再新增
13. 撤销 CommonPreset,61 个方法按场景重新分组;Builder 移除 common 映射(调用会抛异常),新增 6 组映射
    - ArticlePreset(article):title/author/description/intro/content/tag
    - GoodsPreset(goods):price/num/stock/attribute,并新增 sku/sales/marketPrice/costPrice(金额沿用「单位分」约定)
    - SpecPreset(spec):size/width/height/weight/unit
    - DatePreset(date):beginTime/startTime/endTime/auditedAt/expiredAt
    - ContactPreset(contact):phone/mobile/email/tel/contact
    - BasicPreset(basic):name/remark/status/sort/type/alias/reason,封闭清单不再新增;与 CommonPreset 的开放式清单相区别
    - 删除 18 个零散/零调用方法;12 个有调用的方法内联回表模板(from/channel/parentId/cateIdMultiple/scene/sceneMultiple/scale/industry/money/result/code/url),保证建表列不变
14. TablePreset 拆成「一个方法一个类」:15 个 tables/templates/*Table.php + 4 个 tables/parts/*Part.php,门面 TablePreset 仅做转发;$builder->table->article() 等 19 个调用面保持不变,拆分前后列定义指纹完全一致
15. README.md 与 database/migrations 示例同步改走新组;修正 4 处失效示例、conent/regionAddress 拼写,以及批量替换 ->gen 时误伤的 user->gender()(曾变成 user->commonder());issues.md 按新结构重写
16. 全量 php -l 通过,13 个预设组实测均可实例化,19 个表模板拆分前后列定义指纹完全一致
17. 精简表预设:门面只保留 category/article/order/payOrder/business/user/file 七个方法
    - 删除 12 个门面方法:region/zone/zoneAddress/industry/carousel/couponRule/voice/employee/userAddress/bank/statusSort/datetime
    - 删除 8 个模板类:RegionTable/CarouselTable/IndustryTable/CouponRuleTable/VoiceTable/EmployeeTable/UserAddressTable/BankTable
    - 删除 parts/ 目录 4 个片段类:ZonePart/ZoneAddressPart/StatusSortPart/DatetimePart;OrderTable/BusinessTable 内联原有列定义,生成列不变
    - 删除 4 个引用已删模板的 database/migrations 历史示例文件
18. README 组合字段示例同步精简,issues.md 按 7 个模板重写;全量 php -l 通过,7 个保留模板列定义指纹与精简前完全一致
19. 撤销运行时表预设,改为文档场景库:表结构属于项目自己的 migration,迁移文件必须自包含,不该依赖包里的整表模板
    - 删除整个 src/preset/tables/(门面 TablePreset.php + 7 个 templates/*Table.php),Builder 移除 table 映射,预设组由 13 个减为 12 个
    - 根目录新建 docs/:18 个 md 文件(17 个业务场景 + 常用片段)与中文索引 README.md,场景名用中文、点击跳转到可直接复制的 change() 代码
    - 场景覆盖:用户/用户地址/员工/商家/企业/银行/分类/文章/轮播图/行政区划/行业/商品/订单/支付订单/优惠券规则/发票/附件文件;其中商品、商家为新增,发票、银行、企业、用户地址等由原已删模板的结构重建
    - 文档同时修正了原模板的几处写法:文章计数列改用 ->default(0)、支付订单 order_no 不带唯一索引、文件 ext 用 string
    - README「组合字段」段落改为指向 docs/README.md,并给出字段组组装示例
    - 删除 database/migrations 下剩余 6 个引用旧表模板的历史示例(含 1 处链式 ->user()->region() 坏引用),本批累计删除 10 个
20. 全量验证:docs/ 21 个代码块逐个实际执行成功并生成预期列数,md 链接全部有效;全部 php 文件 php -l 通过;12 个预设组可实例化,$builder->table 抛异常;issues.md 按新结构重写
21. docs/ 按业务分组整理:18 个场景文档从平铺改为 5 个子目录(account/ 用户与账号 6 个、content/ 内容与展示 5 个、trade/ 商品与交易 5 个、system/ 系统与附件 1 个、parts/ 常用片段 1 个),仅 docs/README.md 留在根目录
    - 文件名全部保持不变;索引 18 个链接加目录前缀,文档内跨组互链(user-address.md -> ../parts/parts.md)同步修正,同组互链(industry.md -> category.md)不变
    - 索引新增目录说明,issues.md 中的路径与结构描述同步;复验 21 个代码块可执行、全部相对链接有效
22. docs/ 场景示例的 $this->builder('') 全部填充为对应的中文表注释,不再传空串
    - 依据:builder() 的字符串参数经 Migrator::builder() 原样传给 Builder::__construct(),构造函数写入 options['comment'](src/Builder.php:98),Migrator::invokeTable() 建表时使用该选项
    - 17 个场景各填对应注释(用户表/订单表/发票表等),索引补充说明该参数含义与不要传空串的约定
    - 验证:17 个代码块逐个执行,getOptions()['comment'] 与文档字符串逐一一致且非空;20 个相对链接仍有效
23. createdAt()/updatedAt()/deletedAt() 从 Builder 下沉到 Migrator::create(),Builder 只保留 DDL 原语与表选项
    - 理由:这三个方法写死列名/注释/类型,不是构建类型;且全项目仅 Migrator::create() 3 处调用、用户侧 0 处,属于框架无条件注入的审计列,不该放进供用户挑选的 DatePreset(会让框架核心路径依赖 __get 映射表,也会诱导用户重复手写)
    - create() 内联为 dateTime('created_at','创建时间') / dateTime('updated_at','更新时间') / if ($soft_delete) dateTime('deleted_at','删除时间',0,true);原 precision 参数全项目无非 0 调用,直接固定为 0
    - 判断标准:用户要主动调用的快捷方法(如 id())留在 Builder,框架自己注入的收进 Migrator
    - 验证:软删开/关两种情况下三列定义 md5 指纹与改动前完全一致;21 个 docs 代码块、20 个链接、41 处 README 示例、12 个预设组全项通过

#### v2.0.1

1. 修改comment_num字段的comment错误(点赞数量->评论错误)
2. 修改composer.json中homepage的指向错误

#### v2.0.0

1. 支持php:^7.1
2. 将常规字段从构建器中剔除,防止混淆
3. 优化相关模版