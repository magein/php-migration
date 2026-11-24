<?php

namespace Tests\Support;

use Phinx\Db\Adapter\MysqlAdapter;
use Phinx\Db\Table\Column;

/**
 * 把包装层列定义转成 MySQL 列定义 SQL 的内存探针
 * 不连接数据库，仅 stub 掉 getAttribute / getConnection 两个依赖
 */
class SqlDefinitionProbe extends MysqlAdapter
{
    public function __construct()
    {
    }

    public function getAttribute(int $attribute)
    {
        return '5.7.34';
    }

    public function getConnection(): \PDO
    {
        return new \PDO('sqlite::memory:');
    }

    /**
     * @param Column $column
     * @return string
     */
    public function definition(Column $column): string
    {
        $method = new \ReflectionMethod(MysqlAdapter::class, 'getColumnSqlDefinition');
        $method->setAccessible(true);

        return $method->invoke($this, $column);
    }
}
