<?php

use magein\migration\Migrator;

/**
 * 新增列：为用户表补充资料字段
 * 演示：up() 用 update() 加列、->after() 指定列位置、down() 用 Phinx 原生 removeColumn() 链式删回。
 * 注意：不要与更晚的 20250811073741_user_append.php 重复（那边加 points/score/id_type/id_number）。
 */
class AppendUserProfile extends Migrator
{
    protected string $name = 'user';

    public function up()
    {
        $builder = $this->builder();

        $builder->tinyInteger('age', '年龄');
        $builder->unsignedInteger('member_id', '会员id');
        $builder->string('wechat_openid', '微信 openid', 64)->default('')->after('email');

        $this->update($builder);
    }

    public function down()
    {
        $this->table($this->name)
            ->removeColumn('age')
            ->removeColumn('member_id')
            ->removeColumn('wechat_openid')
            ->save();
    }
}
