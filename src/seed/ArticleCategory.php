<?php


namespace magein\migration\seed;

use Phinx\Seed\AbstractSeed;

class ArticleCategory extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     */

    protected $name = 'article_category';

    public function run(): void
    {
        $data = [
            [
                'name' => '公司新闻',
                'parent_id' => 0,
                'status' => 1,
                'sort' => 10,
                'remark' => '企业最新动态和新闻',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => '行业资讯',
                'parent_id' => 0,
                'status' => 1,
                'sort' => 20,
                'remark' => '相关行业动态信息',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => '产品知识',
                'parent_id' => 0,
                'status' => 1,
                'sort' => 30,
                'remark' => '产品使用和功能介绍',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => '技术文章',
                'parent_id' => 0,
                'status' => 1,
                'sort' => 40,
                'remark' => '专业技术分享',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => '常见问题',
                'parent_id' => 0,
                'status' => 1,
                'sort' => 50,
                'remark' => '常见问题解答',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => '媒体报道',
                'parent_id' => 0,
                'status' => 1,
                'sort' => 60,
                'remark' => '媒体对公司的报道',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => '活动公告',
                'parent_id' => 0,
                'status' => 1,
                'sort' => 70,
                'remark' => '企业活动信息',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            // 二级分类示例
            [
                'name' => '产品更新',
                'parent_id' => 3, // 产品知识的子分类
                'status' => 1,
                'sort' => 1,
                'remark' => '产品版本更新信息',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $table = $this->table($this->name);

        // 遍历插入数据
        foreach ($data as $item) {
            // 检查银行名称是否已存在
            $exists = $this->fetchRow(
                "SELECT id FROM {$this->name} WHERE name = '{$item['name']}' LIMIT 1"
            );
            if (!$exists) {
                $table->insert($item)->save();
            }
        }

    }
}
