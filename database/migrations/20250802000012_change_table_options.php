<?php

use magein\migration\Migrator;

/**
 * 更新表属性：修改商品表的表注释
 * 演示：changeComment()->save() 改表级注释；引擎 / 字符集这类表选项
 *       在建表时用 schema->setEngineInnodb() / schema->setCollation() 设定，之后改动用 execute() 写原生 DDL。
 */
class ChangeTableOptions extends Migrator
{
    protected $name = 'goods';

    public function up()
    {
        $this->table($this->name)->changeComment('商品主表 v2')->save();
    }

    public function down()
    {
        $this->table($this->name)->changeComment('商品表')->save();
    }
}
