<?php

use magein\migration\Migrator;

/**
 * 更新列：把商品价格从整数改为定点数
 * 演示：Builder::change($name, $type, $comment) 走 Phinx changeColumn，
 *       第二个参数是 Phinx 类型串（integer / decimal / string / text ...），down 改回原类型。
 */
class AlterGoodsPrice extends Migrator
{
    protected $name = 'goods';

    public function up()
    {
        $builder = $this->builder();
        $builder->change('price', 'decimal', '商品售价 单位分');

        $this->update($builder);
    }

    public function down()
    {
        $builder = $this->builder();
        $builder->change('price', 'integer', '售价 单位分');

        $this->update($builder);
    }
}
