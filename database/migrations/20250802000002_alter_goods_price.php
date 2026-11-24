<?php

use magein\migration\Migrator;

/**
 * 更新列：把商品价格从整数改为定点数
 * 演示：Builder::change($name, $type, $comment) 走 Phinx changeColumn，
 *       第二个参数是 Phinx 类型串（integer / decimal / string / text ...），down 改回原类型。
 * 注意：change() 是整列重建，只带「类型 + 注释」，default / null / 精度必须链式重写，
 *       不写就等于清空原属性；decimal 不给 precision / scale 会生成裸 DECIMAL。
 */
class AlterGoodsPrice extends Migrator
{
    protected string $name = 'goods';

    public function up()
    {
        $builder = $this->builder();
        $builder->change('price', 'decimal', '商品售价 单位分')
            ->default(0)
            ->precision(10)
            ->scale(2);

        $this->update($builder);
    }

    public function down()
    {
        $builder = $this->builder();
        $builder->change('price', 'integer', '价格 单位分')->default(0);

        $this->update($builder);
    }
}
