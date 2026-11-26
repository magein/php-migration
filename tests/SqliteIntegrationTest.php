<?php

namespace tests;

use Phinx\Db\Adapter\SQLiteAdapter;
use Phinx\Db\Table;
use PHPUnit\Framework\TestCase;

/**
 * SQLite 内存库真实执行 DDL 的冒烟测试
 */
class SqliteIntegrationTest extends TestCase
{
    /**
     * @var SQLiteAdapter
     */
    private $adapter;

    protected function setUp(): void
    {
        $this->adapter = new SQLiteAdapter(['memory' => true]);
    }

    private function usersTable(): Table
    {
        // 不显式加 id 列：SQLiteAdapter 在未设置 id 选项时会自动补自增主键 id
        return (new Table('smoke_users', [], $this->adapter))
            ->addColumn('name', 'string', ['limit' => 64])
            ->addIndex(['name'], ['unique' => true, 'name' => 'uniq_smoke_users_name']);
    }

    public function test_create_table_with_unique_index(): void
    {
        $this->usersTable()->create();

        $this->assertTrue($this->adapter->hasTable('smoke_users'));
        $this->assertTrue($this->adapter->hasColumn('smoke_users', 'name'));
        $this->assertTrue($this->adapter->hasIndex('smoke_users', ['name']));
    }

    public function test_add_and_remove_column(): void
    {
        $table = $this->usersTable();
        $table->create();

        $table->addColumn('extra', 'text')->update();
        $this->assertTrue($this->adapter->hasColumn('smoke_users', 'extra'));

        $table->removeColumn('extra')->update();
        $this->assertFalse($this->adapter->hasColumn('smoke_users', 'extra'));
    }

    public function test_foreign_key(): void
    {
        $this->usersTable()->create();

        (new Table('smoke_children', [], $this->adapter))
            ->addColumn('user_id', 'integer')
            ->addForeignKey('user_id', 'smoke_users', 'id')
            ->create();

        $this->assertTrue($this->adapter->hasTable('smoke_children'));
        $this->assertTrue($this->adapter->hasForeignKey('smoke_children', ['user_id']));
    }

    public function test_custom_composite_primary_key(): void
    {
        $schema = new \magein\migration\Schema();
        $schema->setPrimaryKey(['user_id', 'tenant_id']);

        (new Table('smoke_pk', $schema->toArray(), $this->adapter))
            ->addColumn('user_id', 'integer')
            ->addColumn('tenant_id', 'integer')
            ->create();

        $columns = $this->adapter->getConnection()
            ->query("PRAGMA table_info('smoke_pk')")->fetchAll(\PDO::FETCH_ASSOC);
        $pk = [];
        foreach ($columns as $column) {
            if ($column['pk']) {
                $pk[] = $column['name'];
            }
        }

        $this->assertSame(['user_id', 'tenant_id'], $pk);
    }

    public function test_drop_table(): void
    {
        $table = $this->usersTable();
        $table->create();

        $table->drop()->save();

        $this->assertFalse($this->adapter->hasTable('smoke_users'));
    }
}
