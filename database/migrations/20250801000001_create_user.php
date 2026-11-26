<?php

use magein\migration\Migrator;

/**
 * 建表：用户表
 * 演示：change() 可逆迁移、create() 默认注入 created_at / updated_at / deleted_at、
 *       Builder 原语链式设置、schema->setEngineInnodb() 指定存储引擎。
 */
class CreateUser extends Migrator
{
    protected string $name = 'user';

    public function change()
    {
        $builder = $this->builder('用户表');
        $builder->schema->setEngineInnodb();
        $builder->id();

        $builder->string('username', '登录账号', 30)->unique();
        $builder->string('password', '密码');
        $builder->string('nickname', '昵称', 60)->default('');
        $builder->string('avatar', '头像')->default('');
        $builder->char('phone', '手机号码', 11);
        $builder->string('email', '邮箱地址', 60)->default('');
        $builder->enum('gender', ['0', '1', '2'])->comment('性别 0 未知 secret 1 男 man 2 女 woman')->default('0');
        $builder->date('birthday', '出生年月')->nullable();

        $this->create($builder);
    }
}
