<?php

use magein\migration\Migrator;

/**
 * 建表：商品表
 * 演示：价格 / 库存 / 销量 / 规格与图文内容的常用字段组合（见 docs/trade/goods.md）、
 *       $this->table()->addIndex() 组合索引、schema->setEngineInnodb() 供后续外键与表选项示例使用。
 */
class CreateGoods extends Migrator
{
    protected string $name = 'goods';

    public function change()
    {
        $builder = $this->builder('商品表');
        $builder->schema->setEngineInnodb();
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

        // change() 里禁止 save()，索引动作只能用 update() 提交
        $this->table($this->name)
            ->addIndex(['cate_id', 'status'], ['name' => 'idx_cate_status'])
            ->update();
    }
}
