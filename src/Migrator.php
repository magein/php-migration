<?php

/**
 * @user magein
 * @date 2024/1/25 14:07
 */


namespace magein\migration;

use Phinx\Db\Table;
use Phinx\Migration\AbstractMigration;

class Migrator extends AbstractMigration
{
    /**
     * @var Builder|null
     */
    protected $builder = null;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @param $message
     * @return void
     */
    protected function exception($message)
    {
        echo "文件 : " . static::class;
        echo "\n";
        echo $message;
        exit(1);
    }

    /**
     *
     * namespace Phinx\Migration\MigrationInterface有默认的四个命令
     *
     * public const CHANGE = 'change';
     * public const UP = 'up';
     * public const DOWN = 'down';
     * public const INIT = 'init';
     *
     * @return void
     */
    public function init()
    {
        if (empty($this->name)) {
            $class_name = static::class;
            $class_name = preg_replace(['/^Create/', '/Table$/'], ['', ''], $class_name);
            $class_name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class_name));
            $this->name = $class_name;
        }
    }

    public function builder($options = null): Builder
    {
        return new Builder($options);
    }

    /**
     * @return Table
     */
    public function invokeTable(): Table
    {
        $name = $this->name;
        if (empty($name)) {
            $this->exception('请设置表名称');
        }

        $options = [];
        if ($this->builder) {
            $options = $this->builder->getOptions();
        }

        return $this->table($name, $options);
    }

    /**
     * @param Builder $builder
     * @return $this
     */
    public function setBuilder(Builder $builder): Migrator
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * 组装 Phinx 可识别的列选项
     * @param Column $column
     * @return array
     */
    protected function columnOptions(Column $column): array
    {
        $options = $column->getOthers();

        if ($column->getDefault() !== -1) {
            $options['default'] = $column->getDefault();
        }

        if ($column->getLength()) {
            $options['limit'] = $column->getLength();
        }

        if ($column->getAfter()) {
            $options['after'] = $column->getAfter();
        }

        if ($column->getComment()) {
            $options['comment'] = $column->getComment();
        }

        $options['null'] = $column->getNull();
        if ($column->getNull()) {
            unset($options['default']);
        }

        if (isset($options['increment']) && $options['increment'] === false) {
            unset($options['increment']);
        }

        return $options;
    }

    /**
     * @param Table $table
     * @return Table
     */
    protected function parse(Table $table): Table
    {
        foreach ($this->builder->getRenames() as $rename) {
            $table->renameColumn($rename[0], $rename[1]);
        }

        $removes = $this->builder->getRemoves();
        if ($removes && !$this->isMigratingUp()) {
            throw new \RuntimeException('remove() 只能在 up() 中使用，请在 down() 中显式重建列');
        }
        foreach ($removes as $name) {
            $table->removeColumn($name);
        }

        foreach ($this->builder->getColumns() as $column) {
            /**
             * @var Column $column
             */
            $name = $column->getName();
            $options = $this->columnOptions($column);

            if ($column->isChange()) {
                $table->changeColumn($name, $column->getType(), $options);
                continue;
            }

            $table->addColumn($name, $column->getType(), $options);

            if ($column->getUnique()) {
                $table->addIndex($column->getName(),
                    [
                        'name' => $column->getUnique(),
                        'unique' => true,
                    ]
                );
            } elseif ($column->getIndex()) {
                $table->addIndex($column->getName(), [
                    'name' => $column->getIndex(),
                ]);
            }
        }


        // 批量索引在列声明之后追加；列级 ->index() / ->unique() 是单列索引，不冲突
        if ($this->builder->unique) {
            foreach ($this->builder->unique as $item) {
                $table->addIndex($item[0], $item[1] ?? []);
            }
        } elseif ($this->builder->index) {
            foreach ($this->builder->index as $item) {
                $table->addIndex($item[0], $item[1] ?? []);
            }
        }

        return $table;
    }

    /**
     * 执行create方法
     * @param Builder $builder
     * @param bool $soft_delete
     * @return void
     */
    public function create(Builder $builder, bool $soft_delete = true): void
    {
        $builder->dateTime('created_at', '创建时间');
        $builder->dateTime('updated_at', '更新时间');

        if ($soft_delete) {
            $builder->dateTime('deleted_at', '删除时间', 0, true);
        }

        $this->setBuilder($builder);
        $this->parse($this->invokeTable())->create();
    }

    /**
     * 执行update方法
     * @param Builder $builder
     * @return void
     */
    public function update(Builder $builder): void
    {
        $this->setBuilder($builder);
        $this->parse($this->invokeTable())->update();
    }

    /**
     * 执行save方法
     * @param Builder $builder
     * @return void
     */
    public function save(Builder $builder): void
    {
        $this->setBuilder($builder);
        $this->parse($this->invokeTable())->save();
    }

    /**
     * 表是否存在
     * @return bool
     */
    public function exist(): bool
    {
        return $this->hasTable($this->name);
    }

    /**
     * 删除表
     * @return void
     */
    public function drop(): void
    {
        if ($this->hasTable($this->name)) {
            $this->invokeTable()->drop()->save();
        }
    }

    /**
     * @param $column
     * @return bool
     */
    public function hasColumn($column): bool
    {
        return $this->invokeTable()->hasColumn($column);
    }

    /**
     * 添加索引
     * @param array|string $columns
     * @param array $options
     * @return void
     */
    public function addIndex($columns, array $options = []): void
    {
        $this->invokeTable()->addIndex($columns, $options)->save();
    }

    /**
     * 添加唯一措施一
     * @param array|string $columns
     * @param string|null $name
     * @return void
     */
    public function addUniqueIndex($columns, string $name = null): void
    {
        $this->invokeTable()->addIndex($columns, [
            'unique' => true,
            'name' => $name ?: (is_array($columns) ? implode('_', $columns) : $columns)
        ])->save();
    }

    /**
     * 删除索引根据字段
     * @param string|string[] $columns
     * @return void
     */
    public function removeIndex($columns): void
    {
        if ($this->invokeTable()->hasIndex($columns)) {
            $this->invokeTable()->removeIndex($columns)->save();
        }
    }

    /**
     * 删除列
     * @param $columns
     * @return void
     */
    public function removeColumn(...$columns): void
    {
        if ($columns) {

            $table = $this->invokeTable();

            foreach ($columns as $column) {
                if ($table->hasColumn($column)) {
                    $table->removeColumn($column)->save();
                }
            }

        }
    }

    /**
     * 删除索引根据索引名称
     * @param $name
     * @return void
     */
    public function removeIndexByName($name): void
    {
        if ($this->invokeTable()->hasIndexByName($name)) {
            $this->invokeTable()->removeIndexByName($name)->save();
        }
    }

    /**
     * 删除列
     * @param string $new_name
     * @return void
     */
    public function rename(string $new_name): void
    {
        if ($new_name) {
            $this->invokeTable()->rename($new_name);
        }
    }

    /**
     * 添加外键
     * @param $columns
     * @param $referencedTable
     * @param string|string[] $referencedColumns
     * @param array $options
     * @return void
     */
    public function addForeignKey($columns, $referencedTable, $referencedColumns = ['id'], array $options = []): void
    {
        $this->invokeTable()->addForeignKey($columns, $referencedTable, $referencedColumns, $options)->save();
    }

    /**
     * 删除外键
     * @param $columns
     * @param string|null $constraint
     * @return void
     */
    public function dropForeignKey($columns, string $constraint = null): void
    {
        if ($this->invokeTable()->hasForeignKey($columns)) {
            $this->invokeTable()->dropForeignKey($columns, $constraint)->save();
        }
    }
}