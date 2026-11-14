# 商品表

商品主档：名称、货号、分类、封面、简介详情、三种价格（单位分）、库存销量、规格。

**表名：`goods`**

> `GoodsPreset` 的金额统一「单位分」，`price / marketPrice / costPrice` 都用 integer。

```php
$builder = $this->builder('商品表');
$builder->id();
$builder->basic->name('商品名称');
$builder->goods->sku();
$builder->string('cate_id', '所属分类')->default('');
$builder->image->cover();
$builder->article->intro();
$builder->article->content();
$builder->goods->marketPrice();
$builder->goods->price();
$builder->goods->costPrice();
$builder->goods->stock();
$builder->goods->sales();
$builder->spec->size();
$builder->spec->weight();
$builder->spec->unit();
$builder->basic->status();
$builder->basic->sort();
$builder->basic->remark();
$this->create($builder);
```
