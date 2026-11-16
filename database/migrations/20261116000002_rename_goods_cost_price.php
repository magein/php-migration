<?php

use magein\migration\Migrator;

/**
 * 重命名列：成本价列改名 cost_price -> cost_amount
 * 演示：Phinx 原生 renameColumn()，由 MySQL 适配器读 SHOW FULL COLUMNS 重建列定义，
 *       类型 / NULL / 默认值 / 注释 / 额外属性全部原样保留，不用再给类型。
 */
class RenameGoodsCostPrice extends Migrator
{
    protected string $name = 'goods';

    public function up()
    {
        $this->table($this->name)->renameColumn('cost_price', 'cost_amount')->save();
    }

    public function down()
    {
        $this->table($this->name)->renameColumn('cost_amount', 'cost_price')->save();
    }
}
