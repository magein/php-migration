<?php

use magein\migration\Migrator;

/**
 * 建表：商品-分类关联表（docs 之外补充的多对多场景）
 * 演示：不用预设组的纯 Builder 原语、addUniqueKey() 联合唯一约束代替复合主键、
 *       innoDB() 供后续 add_foreign_key 示例引用。
 */
class CreateGoodsCategory extends Migrator
{
    protected $name = 'goods_category';

    public function change()
    {
        $builder = $this->builder('商品分类关联表');
        $builder->innoDB();
        $builder->id();

        $builder->unsignedInteger('goods_id', '商品 id')->default(0);
        $builder->integer('cate_id', '分类 id')->default(0);
        $builder->integer('sort', '排序')->default(0);

        $builder->addUniqueKey(['goods_id', 'cate_id'], 'uk_goods_cate');

        $this->create($builder, false);
    }
}
