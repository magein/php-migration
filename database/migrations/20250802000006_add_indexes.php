<?php

use magein\migration\Migrator;

/**
 * 索引变更：给商品表加组合索引与唯一索引
 * 演示：addIndex() / addUniqueIndex()，down() 分别用 removeIndex() / removeIndexByName()。
 * 注意：索引名不要与建表时的 idx_cate_status 重复。
 */
class AddIndexes extends Migrator
{
    protected $name = 'goods';

    public function up()
    {
        $this->addIndex(['status', 'sort'], ['name' => 'idx_status_sort']);
        $this->addUniqueIndex('sku', 'uk_sku');
    }

    public function down()
    {
        $this->removeIndex(['status', 'sort']);
        $this->removeIndexByName('uk_sku');
    }
}
