<?php

use magein\migration\Migrator;

/**
 * 删字段 + 删索引（写在 up 侧）
 * 演示：删除动作同样要 up() 里主动执行，并先用 hasColumn() / hasIndexByName() 判断存在，
 *       否则重复执行或回滚到一半再跑都会因为「对象不存在」直接报错。
 * 注意：依赖 20261115115150_rename_user_to_users，表名已经是 users。
 */
class RemoveLegacyColumnAndIndex extends Migrator
{
    protected string $name = 'users';

    public function up()
    {
        // 删字段：旧会员体系遗留列
        $table = $this->table($this->name);
        if ($table->hasColumn('member_id')) {
            $table->removeColumn('member_id');
        }
        $table->save();

        // 删索引：按名删除；按列删除的写法见 20250802000006_add_indexes 的 down()
        $goods = $this->table('goods');
        if ($goods->hasIndexByName('idx_status_sort')) {
            $goods->removeIndexByName('idx_status_sort');
        }
        $goods->save();
    }

    public function down()
    {
        // 加回字段：按原定义还原（unsignedInteger + 注释「会员id」）
        $builder = $this->builder();
        $builder->unsignedInteger('member_id', '会员id');
        $this->update($builder);

        // 加回索引：列与索引名都要与原来一致
        $this->table('goods')
            ->addIndex(['status', 'sort'], ['name' => 'idx_status_sort'])
            ->save();
    }
}
