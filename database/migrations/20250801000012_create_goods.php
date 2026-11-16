<?php

use magein\migration\Migrator;

/**
 * 建表：商品表
 * 演示：价格 / 库存 / 销量 / 规格预设组、图文内容预设、addIndex() 组合索引、
 *       schema->innoDB() 供后续外键与表选项示例使用。
 */
class CreateGoods extends Migrator
{
    protected $name = 'goods';

    public function change()
    {
        $builder = $this->builder('商品表');
        $builder->schema->innoDB();
        $builder->schema->id();

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

        $builder->addIndex(['cate_id', 'status'], ['name' => 'idx_cate_status']);

        $this->create($builder);
    }
}
