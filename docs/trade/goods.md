# 商品表

商品主档：名称、货号、分类、封面、简介详情、三种价格（单位分）、库存销量、规格。

**表名：`goods`**

> 金额统一「单位分」，`price / market_price / cost_price` 都用 integer。

```php
$builder = $this->builder('商品表');
$builder->id();
$builder->string('name', '商品名称', 60);
$builder->string('sku', '货号', 60)->default('');
$builder->string('cate_id', '所属分类')->default('');
$builder->string('cover', '封面图')->default('');
$builder->string('intro', '简介', 140)->default('');
$builder->text('content', '内容')->nullable();
$builder->integer('market_price', '市场价 单位分')->default(0);
$builder->integer('price', '价格 单位分')->default(0);
$builder->integer('cost_price', '成本价 单位分')->default(0);
$builder->integer('stock', '库存')->default(0);
$builder->integer('sales', '销量')->default(0);
$builder->string('size', '尺寸', 60)->default('');
$builder->integer('weight', '克重')->default(0);
$builder->integer('unit', '单位')->default(0);
$builder->tinyInteger('status', '状态 0 禁用 forbid 1 启用 open')->default(1);
$builder->smallInteger('sort', '排序 正序')->default(99);
$builder->string('remark', '备注')->default('');
$this->create($builder);
```
