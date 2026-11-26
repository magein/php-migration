<?php

use magein\migration\Migrator;

class UserAppend extends Migrator
{
    protected string $name = 'user';

    public function up()
    {
        $builder = $this->builder();

        $builder->integer('points', '积分')->default(0);
        $builder->integer('score', '分数')->default(0);
        $builder->tinyInteger('id_type', '证件类型 1 身份证 id 2 港澳通行证 hk_macao 3 tai_wan  4 护照 passport 99 其他 other')->default(1);
        $builder->string('id_number', '证件号码', 30)->default('');

        $this->update($builder);
    }

    public function down()
    {
        $table = $this->table($this->name);

        foreach (['points', 'score', 'id_type', 'id_number'] as $column) {
            if ($table->hasColumn($column)) {
                $table->removeColumn($column);
            }
        }

        $table->save();
    }
}