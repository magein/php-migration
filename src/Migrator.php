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
    protected ?Builder $builder = null;

    /**
     * @var string
     */
    protected string $name = '';

    /**
     * @param string $message
     * @return void
     */
    protected function exception(string $message): void
    {
        throw new \RuntimeException(static::class . ': ' . $message);
    }

    public function builder($options = null): Builder
    {
        return new Builder($options);
    }

    /**
     * @return Table
     */
    protected function invokeTable(): Table
    {
        $name = $this->name;
        if (empty($name)) {
            $this->exception('请设置表名称');
        }

        $options = [];
        if ($this->builder) {
            $options = $this->builder->schema->toArray();
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

        if ($column->hasDefault()) {
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

        // nullable 只决定 NULL / NOT NULL，与 default 相互独立，NULL DEFAULT 0 由上游正常生成
        $options['null'] = $column->getNull();

        // increment 是 SQL Server 的 IDENTITY 步长；自增开关统一用 identity（Phinx 各适配器通用）
        if (!empty($options['increment'])) {
            $options['identity'] = true;
        }
        unset($options['increment']);

        return $options;
    }

    /**
     * @param Table $table
     * @return Table
     */
    protected function parse(Table $table): Table
    {
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

        return $table;
    }

    /**
     * 执行create方法
     * @param Builder $builder
     * @param bool $created_at
     * @param bool $soft_delete
     * @return void
     */
    public function create(Builder $builder, bool $created_at = true, bool $soft_delete = true): void
    {
        if ($created_at) {
            $builder->dateTime('created_at', '创建时间');
            $builder->dateTime('updated_at', '更新时间');
        }

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
}