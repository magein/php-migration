<?php

use Phinx\Seed\AbstractSeed;

/**
 * 用户表基础数据
 *
 * 演示：execute() 与 table()->insert()->saveData()；按 username 先删后插，
 *       重复执行 seed:run 不会触发唯一键冲突。
 * create() 注入的 created_at / updated_at 是非空列，插入时必须显式给值。
 */
class UserSeeder extends AbstractSeed
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        $rows = [
            [
                'username' => 'admin',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'nickname' => '管理员',
                'phone' => '13800000001',
                'email' => 'admin@example.com',
                'age' => 30,
                'member_id' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'username' => 'demo',
                'password' => password_hash('123456', PASSWORD_DEFAULT),
                'nickname' => '演示账号',
                'phone' => '13800000002',
                'email' => 'demo@example.com',
                'age' => 20,
                'member_id' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        $this->execute("DELETE FROM `user` WHERE `username` IN ('admin', 'demo')");
        $this->table('user')->insert($rows)->saveData();
    }
}
