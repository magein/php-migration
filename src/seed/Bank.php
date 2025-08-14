<?php


namespace magein\migration\seed;

use Phinx\Seed\AbstractSeed;

class Bank extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */
    public function run(): void
    {
        $banks = [
            ['name' => '中国工商银行', 'alias' => 'ICBC', 'first_letter' => 'G'],
            ['name' => '中国建设银行', 'alias' => 'CCB', 'first_letter' => 'J'],
            ['name' => '中国农业银行', 'alias' => 'ABC', 'first_letter' => 'N'],
            ['name' => '中国银行', 'alias' => 'BOC', 'first_letter' => 'Z'],
            ['name' => '交通银行', 'alias' => 'BCM', 'first_letter' => 'J'],
            ['name' => '招商银行', 'alias' => 'CMB', 'first_letter' => 'Z'],
            ['name' => '中国邮政储蓄银行', 'alias' => 'PSBC', 'first_letter' => 'Y'],
            ['name' => '中信银行', 'alias' => 'CITIC', 'first_letter' => 'Z'],
            ['name' => '浦发银行', 'alias' => 'SPDB', 'first_letter' => 'P'],
            ['name' => '民生银行', 'alias' => 'CMBC', 'first_letter' => 'M'],
            ['name' => '兴业银行', 'alias' => 'CIB', 'first_letter' => 'X'],
            ['name' => '平安银行', 'alias' => 'PAB', 'first_letter' => 'P'],
            ['name' => '广发银行', 'alias' => 'CGB', 'first_letter' => 'G'],
            ['name' => '华夏银行', 'alias' => 'HXB', 'first_letter' => 'H'],
            ['name' => '光大银行', 'alias' => 'CEB', 'first_letter' => 'G'],
        ];

        // 获取表实例
        $table = $this->table('bank');

        // 遍历插入数据
        foreach ($banks as $bank) {
            // 检查银行名称是否已存在
            $exists = $this->fetchRow(
                "SELECT id FROM bank WHERE name = '{$bank['name']}' LIMIT 1"
            );

            if (!$exists) {
                $table->insert([
                    'name' => $bank['name'],
                    'alias' => $bank['alias'],
                    'icon' => '',
                    'first_letter' => $bank['first_letter'],
                    'status' => 1,
                    'sort' => 99,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ])->saveData();
            }
        }
    }
}
