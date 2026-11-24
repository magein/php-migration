<?php

use magein\migration\Migrator;

/**
 * 建表：商品-分类关联表（docs 之外补充的多对多场景）
 * 演示：不用预设组的纯 Builder 原语、Phinx 原生 addIndex() 联合唯一约束代替复合主键、
 *       schema->setEngineInnodb() 供后续 add_foreign_key 示例引用。
 */
class CreateGoodsCategory extends Migrator
{
    protected string $name = 'goods_category';

    public function change()
    {
        $builder = $this->builder('商品分类关联表');
        $builder->schema->setEngineInnodb();
        $builder->id();

        $builder->unsignedInteger('goods_id', '商品 id')->default(0);
        $builder->integer('cate_id', '分类 id')->default(0);
        $builder->integer('sort', '排序')->default(0);

        // 关联表不需要软删：保留自动注入的时间戳，只关掉 deleted_at
        $this->create($builder, true, false);

        // change() 里禁止 save()，索引动作只能用 update() 提交
        $this->table($this->name)
            ->addIndex(['goods_id', 'cate_id'], ['name' => 'uk_goods_cate', 'unique' => true])
            ->update();
    }
}
