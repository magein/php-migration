<?php

use magein\migration\Migrator;

/**
 * 幂等建表：系统配置表
 * 演示：save() 与 create() 的区别——save() 在表不存在时 create、已存在时 update，
 *       适合反复执行也不报错的初始化脚本；hasTable() 可做额外守卫。
 */
class SaveIdempotent extends Migrator
{
    protected $name = 'demo_config';

    public function up()
    {
        $builder = $this->builder('系统配置表');
        $builder->id();

        $builder->string('config_key', '配置键', 60)->unique();
        $builder->text('config_value', '配置值')->nullable();
        $builder->string('config_group', '分组', 30)->default('');
        $builder->basic->remark('配置说明');

        $this->save($builder);
    }

    public function down()
    {
        if ($this->hasTable($this->name)) {
            $this->table($this->name)->drop()->save();
        }
    }
}
