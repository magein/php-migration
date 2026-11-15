<?php

use magein\migration\Migrator;

/**
 * 外键变更：关联表 goods_id 指向商品表主键
 * 演示：addForeignKey() / dropForeignKey()。
 * 前提：两张表都必须是 InnoDB（见 create_goods.php 与 create_goods_category.php），
 *       且外键列与主键列的类型、符号一致。
 */
class AddForeignKey extends Migrator
{
    protected $name = 'goods_category';

    public function up()
    {
        $this->addForeignKey('goods_id', 'goods', ['id'], [
            'delete' => 'CASCADE',
            'update' => 'CASCADE',
        ]);
    }

    public function down()
    {
        $this->dropForeignKey('goods_id');
    }
}
