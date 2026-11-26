<?php

namespace tests\support;

use magein\migration\Builder;
use magein\migration\Migrator;

/**
 * 提取包装层 columnOptions() 的产物：列名 => [类型, options]
 * Migrator 构造函数为 final，用 newInstanceWithoutConstructor 绕过
 */
class ColumnOptionsProbe
{
    /**
     * @param Builder $builder
     * @return array
     */
    public static function of(Builder $builder): array
    {
        $migrator = (new \ReflectionClass(Migrator::class))->newInstanceWithoutConstructor();
        $method = new \ReflectionMethod(Migrator::class, 'columnOptions');
        $method->setAccessible(true);

        $options = [];
        foreach ($builder->getColumns() as $column) {
            $options[$column->getName()] = [
                $column->getType(),
                $method->invoke($migrator, $column),
            ];
        }

        return $options;
    }
}
