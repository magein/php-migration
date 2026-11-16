<?php

use magein\migration\Migrator;

/**
 * 改表选项：把系统配置表切到 MyISAM，再切回 InnoDB
 * 演示：schema->setEngine*() / setCollation() 只在建表时生效，之后要改引擎、字符集
 *       只能用 execute() 写原生 DDL（Phinx 的 changeComment() 只覆盖注释）。
 * 注意：MyISAM 不支持事务与外键，切换前确认该表没有外键引用。
 */
class ChangeDemoConfigEngine extends Migrator
{
    protected string $name = 'demo_config';

    public function up()
    {
        $this->execute(sprintf('ALTER TABLE `%s` ENGINE=MyISAM', $this->name));
    }

    public function down()
    {
        $this->execute(sprintf('ALTER TABLE `%s` ENGINE=InnoDB', $this->name));
    }
}
