<?php

namespace magein\migration\seed;

use Phinx\Seed\AbstractSeed;

class Industry extends AbstractSeed
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
        $industries = [
            // 一级行业（parent_id = 0）
            ['name' => 'IT/互联网', 'alias' => 'internet', 'parent_id' => 0],
            ['name' => '金融', 'alias' => 'finance', 'parent_id' => 0],
            ['name' => '医疗健康', 'alias' => 'medical', 'parent_id' => 0],
            ['name' => '教育', 'alias' => 'education', 'parent_id' => 0],
            ['name' => '制造业', 'alias' => 'manufacturing', 'parent_id' => 0],
            ['name' => '房地产', 'alias' => 'real_estate', 'parent_id' => 0],
            ['name' => '零售/电商', 'alias' => 'retail', 'parent_id' => 0],
            ['name' => '文化娱乐', 'alias' => 'entertainment', 'parent_id' => 0],
            ['name' => '物流运输', 'alias' => 'logistics', 'parent_id' => 0],
            ['name' => '能源环保', 'alias' => 'energy', 'parent_id' => 0],

            // 二级行业 - IT/互联网子类
            ['name' => '软件开发', 'alias' => 'software', 'parent_id' => 1],
            ['name' => '人工智能', 'alias' => 'ai', 'parent_id' => 1],
            ['name' => '电子商务', 'alias' => 'ecommerce', 'parent_id' => 1],
            ['name' => '游戏', 'alias' => 'game', 'parent_id' => 1],
            ['name' => '网络安全', 'alias' => 'cybersecurity', 'parent_id' => 1],

            // 二级行业 - 金融子类
            ['name' => '银行', 'alias' => 'bank', 'parent_id' => 2],
            ['name' => '证券', 'alias' => 'securities', 'parent_id' => 2],
            ['name' => '保险', 'alias' => 'insurance', 'parent_id' => 2],
            ['name' => '投资', 'alias' => 'investment', 'parent_id' => 2],

            // 二级行业 - 医疗健康子类
            ['name' => '医院', 'alias' => 'hospital', 'parent_id' => 3],
            ['name' => '医药', 'alias' => 'pharmaceutical', 'parent_id' => 3],
            ['name' => '医疗器械', 'alias' => 'medical_device', 'parent_id' => 3],
        ];

        $table = $this->table('industry');

        foreach ($industries as $industry) {
            $exists = $this->fetchRow(
                "SELECT id FROM industry WHERE name = '{$industry['name']}' AND parent_id = {$industry['parent_id']} LIMIT 1"
            );

            if (!$exists) {
                $table->insert([
                    'parent_id' => $industry['parent_id'],
                    'name' => $industry['name'],
                    'alias' => $industry['alias'],
                    'status' => 1,  // 状态启用
                    'sort' => 99,  // 默认排序
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ])->saveData(); // 逐条插入
            }
        }
    }
}
