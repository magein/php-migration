<?php

namespace Tests;

use magein\migration\Builder;
use Phinx\Db\Table\Column;
use PHPUnit\Framework\TestCase;
use Tests\Support\ColumnOptionsProbe;
use Tests\Support\SqlDefinitionProbe;

class BuilderTest extends TestCase
{
    /**
     * @var SqlDefinitionProbe
     */
    private $probe;

    protected function setUp(): void
    {
        $this->probe = new SqlDefinitionProbe();
    }

    /**
     * 把 builder 里指定列转成最终 MySQL 列定义
     * @param Builder $builder
     * @param string $name
     * @return string
     */
    private function sql(Builder $builder, string $name): string
    {
        $column_options = ColumnOptionsProbe::of($builder);
        $this->assertArrayHasKey($name, $column_options);

        [$type, $options] = $column_options[$name];

        $column = new Column();
        $column->setName($name)->setType($type)->setOptions($options);

        return $this->probe->definition($column);
    }

    public function test_string_options_become_comment(): void
    {
        $builder = new Builder('用户表');

        $this->assertSame('用户表', $builder->schema->toArray()['comment']);
        $this->assertFalse($builder->schema->toArray()['id']);
        $this->assertSame('InnoDB', $builder->schema->toArray()['engine']);
        $this->assertSame('utf8_unicode_ci', $builder->schema->toArray()['collation']);
    }

    public function test_array_options_keep_other_defaults(): void
    {
        $builder = new Builder(['comment' => '注释', 'engine' => 'MyISAM']);

        $options = $builder->schema->toArray();
        $this->assertSame('注释', $options['comment']);
        $this->assertSame('MyISAM', $options['engine']);
        $this->assertSame('utf8_unicode_ci', $options['collation']);
        $this->assertFalse($options['id']);
    }

    public function test_unknown_option_is_rejected(): void
    {
        $builder = new Builder(['comment' => '表', 'not_in_whitelist' => 1]);

        $this->assertArrayNotHasKey('not_in_whitelist', $builder->schema->toArray());
    }

    public function test_id_shortcut_on_builder(): void
    {
        $builder = new Builder('用户表');
        $this->assertFalse($builder->schema->toArray()['id']);

        $builder->id();
        $this->assertTrue($builder->schema->toArray()['id']);
    }

    public function test_increments_marks_primary_key(): void
    {
        $builder = new Builder('用户表');
        $builder->integerIncrements('uid');

        $this->assertFalse($builder->schema->toArray()['id']);
        $this->assertSame(['uid'], $builder->schema->toArray()['primary_key']);
    }

    public function test_increments_rejects_existing_primary_key(): void
    {
        $builder = new Builder('用户表');
        $builder->id();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('自增主键与已声明的主键冲突');
        $builder->integerIncrements();
    }

    public function test_increments_rejects_second_auto_column(): void
    {
        $builder = new Builder('用户表');
        $builder->integerIncrements();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('一张表只能有一个自增列');
        $builder->bigIncrements('big_id');
    }

    public function test_duplicate_column_throws(): void
    {
        $builder = new Builder();
        $builder->integer('name');

        $this->expectException(\RuntimeException::class);
        $builder->string('name');
    }

    /**
     * @dataProvider columnDefinitionProvider
     * @param \Closure $builder_factory
     * @param string $column_name
     * @param string $expected
     */
    public function test_column_sql_definitions(\Closure $builder_factory, string $column_name, string $expected): void
    {
        $this->assertSame($expected, $this->sql($builder_factory(), $column_name));
    }

    public function columnDefinitionProvider(): array
    {
        $factory = function (\Closure $fill) {
            return function () use ($fill) {
                $builder = new Builder('探针表');
                $fill($builder);

                return $builder;
            };
        };

        return [
            'integer' => [$factory(function (Builder $b) { $b->integer('int_col'); }), 'int_col', 'INT(11) NOT NULL'],
            'unsigned integer' => [$factory(function (Builder $b) { $b->unsignedInteger('uint_col'); }), 'uint_col', 'INT(11) unsigned NOT NULL'],
            'string' => [$factory(function (Builder $b) { $b->string('str_col'); }), 'str_col', 'VARCHAR(255) NOT NULL'],
            'char with limit' => [$factory(function (Builder $b) { $b->char('char_col', null, 32); }), 'char_col', 'CHAR(32) NOT NULL'],
            'decimal' => [$factory(function (Builder $b) { $b->decimal('dec_col'); }), 'dec_col', 'DECIMAL(10,2) NOT NULL'],
            'unsigned decimal' => [$factory(function (Builder $b) { $b->decimalUnsigned('decol_u'); }), 'decol_u', 'DECIMAL(10,2) unsigned NOT NULL'],
            'boolean' => [$factory(function (Builder $b) { $b->boolean('bool_col'); }), 'bool_col', 'TINYINT(1) NOT NULL'],
            'datetime without precision' => [$factory(function (Builder $b) { $b->dateTime('dt_col'); }), 'dt_col', 'DATETIME NOT NULL'],
            'datetime with milliseconds' => [$factory(function (Builder $b) { $b->dateTime('dt_ms', null, 3); }), 'dt_ms', 'DATETIME(3) NOT NULL'],
            'timestamp with microseconds' => [$factory(function (Builder $b) { $b->timestamp('ts_col', null, 6); }), 'ts_col', 'TIMESTAMP(6) NOT NULL'],
            'json' => [$factory(function (Builder $b) { $b->json('json_col'); }), 'json_col', 'JSON NOT NULL'],
            'big increments' => [$factory(function (Builder $b) { $b->bigIncrements('big_inc'); }), 'big_inc', 'BIGINT(20) unsigned NOT NULL AUTO_INCREMENT'],
            'nullable with default 0' => [$factory(function (Builder $b) { $b->string('st')->nullable()->default(0); }), 'st', 'VARCHAR(255) NULL DEFAULT 0'],
            'nullable without default' => [$factory(function (Builder $b) { $b->text('txt')->nullable(); }), 'txt', 'TEXT NULL'],
            'default -1 survives' => [$factory(function (Builder $b) { $b->integer('dft')->default(-1); }), 'dft', 'INT(11) NOT NULL DEFAULT -1'],
            'enum' => [$factory(function (Builder $b) { $b->enum('en_col', ['a', 'b']); }), 'en_col', "ENUM('a', 'b') NOT NULL"],
        ];
    }

    /**
     * change() 只带类型与注释，缺省属性等于整列重建，精度要用链式方法补回来
     */
    public function test_change_column_carries_precision_and_scale(): void
    {
        $builder = new Builder('探针表');
        $builder->change('price', 'decimal', '商品售价 单位分')
            ->default(0)
            ->precision(10)
            ->scale(2);

        $this->assertSame(
            "DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT '商品售价 单位分'",
            $this->sql($builder, 'price')
        );
    }

    /**
     * others() 整体替换，precision() / scale() 是往 others 里加键，两者可以混用
     */
    public function test_others_and_precision_merge(): void
    {
        $builder = new Builder('探针表');
        $column = $builder->change('amount', 'decimal', '金额')
            ->others(['signed' => false])
            ->precision(12)
            ->scale(4);

        $this->assertSame(['signed' => false, 'precision' => 12, 'scale' => 4], $column->getOthers());
        $this->assertSame(
            "DECIMAL(12,4) unsigned NOT NULL COMMENT '金额'",
            $this->sql($builder, 'amount')
        );
    }

    public function test_preset_groups_register_unique_columns(): void
    {
        $groups = [
            'user', 'basic', 'addr', 'file', 'image', 'article',
            'goods', 'spec', 'date', 'contact', 'flag', 'order',
        ];

        foreach ($groups as $group) {
            $builder = new Builder($group . ' 探针');
            $preset = $builder->{$group};

            $count = 0;
            foreach ((new \ReflectionClass($preset))->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                if ($method->getDeclaringClass()->getName() !== get_class($preset)) {
                    continue;
                }

                $this->assertSame(
                    0,
                    $method->getNumberOfRequiredParameters(),
                    "预设 {$group}::{$method->getName()}() 出现必填参数，无法无参构建"
                );

                // 重复列名会由 registerColumn() 抛 RuntimeException 使本测试失败
                $preset->{$method->getName()}();
                $count++;
            }

            $this->assertGreaterThan(0, $count, "预设组 {$group} 没有可调用的方法");
            $names = array_map(function ($column) { return $column->getName(); }, $builder->getColumns());
            $this->assertCount(count($names), array_unique($names), "预设组 {$group} 存在重复列名");
            $this->assertSame($count, count($names), "预设组 {$group} 方法数与列数不一致");
        }
    }

    public function test_spatial_collection_primitives_are_removed(): void
    {
        $reflection = new \ReflectionClass(Builder::class);

        foreach (['geometryCollection', 'multiPoint', 'multiLineString', 'multiPolygon', 'multiPolygonZ'] as $name) {
            $this->assertFalse($reflection->hasMethod($name), "{$name}() 应已移除（上游未映射的类型串）");
        }

        foreach (['geometry', 'point', 'lineString', 'polygon'] as $name) {
            $this->assertTrue($reflection->hasMethod($name), "{$name}() 应保留");
        }
    }
}
