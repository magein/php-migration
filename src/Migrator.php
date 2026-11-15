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
        // unique 与 index 是两组独立动作，分别提交，不能写成 elseif
        if ($this->builder->unique) {
            foreach ($this->builder->unique as $item) {
                $table->addIndex($item[0], $item[1] ?? []);
            }
        }
        if ($this->builder->index) {
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
}