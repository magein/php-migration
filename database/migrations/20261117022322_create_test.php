<?php

use magein\migration\Migrator;

class CreateTest extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * 以下命令可以在此方法中使用，Phinx 将在回滚时自动反转它们:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * 如果希望实现自动升级降级，那就把逻辑写在change方法里，只最终调用create和update方法，不要调用save方法
     *
     * 创建使用 create()、更新使用 update()
     */

    /**
     * @var string
     */
    protected string $name = 'test';

    public function change()
    {
        $builder = $this->builder('');
        $builder->integerIncrements();
        $builder->string('username', '登录账号', 30)->unique();

        $this->save($builder);
    }

    public function down()
    {
        if ($this->hasTable($this->name)) {
            $this->table($this->name)->drop()->save();
        }
    }
}
