<?php

use magein\migration\Migrator;

/**
 * 索引变更：给商品表加组合索引与唯一索引
 * 演示：Phinx 原生 addIndex() / removeIndex() / removeIndexByName()，同一表对象攒动作后一次 save()。
 * 注意：索引名不要与建表时的 idx_cate_status 重复。
 */
class AddIndexes extends Migrator
{
    protected $name = 'goods';

    public function up()
    {
        $this->table($this->name)
            ->addIndex(['status', 'sort'], ['name' => 'idx_status_sort'])
            ->addIndex('sku', ['name' => 'uk_sku', 'unique' => true])
            ->save();
    }

    public function down()
    {
        $table = $this->table($this->name);

        if ($table->hasIndex(['status', 'sort'])) {
            $table->removeIndex(['status', 'sort']);
        }
        if ($table->hasIndexByName('uk_sku')) {
            $table->removeIndexByName('uk_sku');
        }

        $table->save();
    }
}
