<?php

use magein\migration\Migrator;

/**
 * 建表：用户表
 * 演示：change() 可逆迁移、create() 默认注入 created_at / updated_at / deleted_at、
 *       字段预设组与 Builder 原语混用、schema->setEngineInnodb() 指定存储引擎。
 */
class CreateUser extends Migrator
{
    protected string $name = 'user';

    public function change()
    {
        $builder = $this->builder('用户表');
        $builder->schema->setEngineInnodb();
        $builder->id();

        $builder->user->username();
        $builder->user->password();
        $builder->user->nickname();
        $builder->user->avatar();
        $builder->contact->phone();
        $builder->contact->email();
        $builder->user->gender();
        $builder->user->birthday();

        $this->create($builder);
    }
}
