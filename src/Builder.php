<?php

namespace magein\migration;

use magein\migration\preset\fields\AddressPreset;
use magein\migration\preset\fields\ArticlePreset;
use magein\migration\preset\fields\BasicPreset;
use magein\migration\preset\fields\ContactPreset;
use magein\migration\preset\fields\DatePreset;
use magein\migration\preset\fields\FilePreset;
use magein\migration\preset\fields\FlagPreset;
use magein\migration\preset\fields\GoodsPreset;
use magein\migration\preset\fields\ImagePreset;
use magein\migration\preset\fields\OrderPreset;
use magein\migration\preset\fields\SpecPreset;
use magein\migration\preset\fields\UserPreset;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * @property UserPreset user
 * @property BasicPreset basic
 * @property AddressPreset addr
 * @property FilePreset file
 * @property ImagePreset image
 * @property ArticlePreset article
 * @property GoodsPreset goods
 * @property SpecPreset spec
 * @property DatePreset date
 * @property ContactPreset contact
 * @property FlagPreset flag
 * @property OrderPreset order
 */
class Builder
{
    /**
     * 预设组映射：属性名 => 预设类
     * @var array
     */
    private const GROUPS = [
        'user' => UserPreset::class,
        'basic' => BasicPreset::class,
        'addr' => AddressPreset::class,
        'file' => FilePreset::class,
        'image' => ImagePreset::class,
        'article' => ArticlePreset::class,
        'goods' => GoodsPreset::class,
        'spec' => SpecPreset::class,
        'date' => DatePreset::class,
        'contact' => ContactPreset::class,
        'flag' => FlagPreset::class,
        'order' => OrderPreset::class,
    ];

    /**
     * 普通索引
     * @var array
     */
    public array $index = [];

    /**
     * 唯一索引
     * @var array
     */
    public array $unique = [];

    /**
     * 外键
     * @var array
     */
    public array $foreign = [];

    /**
     * 表级选项（引擎、注释、字符集、主键策略等）
     * @var Schema
     */
    public Schema $schema;

    /**
     * 添加的列
     * @var array
     */
    protected array $columns = [];

    /**
     * @param Schema|array|string|null $options Schema 实例、表选项数组或表注释字符串
     */
    public function __construct($options = null)
    {
        $this->schema = Schema::from($options);
    }

    /**
     * 让 Phinx 自动创建自增主键 id。构建时 schema 通常不单独声明、用默认值，
     * 所以这个高频操作直接挂在 Builder 上，转发表选项到 schema
     * @return void
     */
    public function id(): void
    {
        $this->schema->id();
    }

    public function __get($name)
    {
        if (!isset(self::GROUPS[$name])) {
            throw new \InvalidArgumentException("未知的预设组: {$name}");
        }

        $class = self::GROUPS[$name];

        return new $class($this);
    }

    /**
     * 获取所有的列
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * 添加一列
     * @param $type
     * @param $name
     * @param array $parameters
     * @return Column
     */
    public function addColumn($type, $name, array $parameters = []): Column
    {
        $column = new Column();
        $column->name($name);
        $column->type($type);
        $column->others($parameters);

        if (isset($parameters['null']) && $parameters['null']) {
            $column->nullable();
        }

        if (isset($parameters['length']) && $parameters['length']) {
            $column->length($parameters['length']);
        }

        $this->registerColumn($name, $column);

        return $column;
    }

    /**
     * 登记列：addColumn() 与 change() 共用，统一做重复列名检查
     * @param string $name
     * @param Column $column
     * @return void
     */
    protected function registerColumn(string $name, Column $column): void
    {
        if (isset($this->columns[$name])) {
            throw new \RuntimeException("重复的列名: {$name}");
        }

        $this->columns[$name] = $column;
    }

    /**
     * 修改已有列的定义
     * @param string $name
     * @param string $type
     * @param string|null $comment
     * @return Column
     */
    public function change(string $name = '', string $type = '', string $comment = null): Column
    {
        if ($name === '') {
            throw new \RuntimeException('修改列的列名不能为空');
        }

        if ($type === '') {
            throw new \RuntimeException("修改列的类型不能为空: {$name}");
        }

        $column = new Column();
        $column->name($name);
        $column->type($type);
        $column->comment($comment ?: '');
        $column->change();
        $this->registerColumn($name, $column);

        return $column;
    }

    /**
     * 添加索引或者组合索引
     * @param array|string $columns
     * @param array|string|null $options 数组为索引选项，字符串为索引名
     * @return void
     */
    public function addIndex($columns, $options = null): void
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $this->index[] = [$columns, $this->normalizeIndexOptions($columns, $options)];
    }

    /**
     * 添加唯一索引
     * @param array|string $columns
     * @param array|string|null $options 数组为索引选项，字符串为索引名
     * @return void
     */
    public function addUniqueKey($columns, $options = null): void
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $options = $this->normalizeIndexOptions($columns, $options);
        $options['unique'] = true;

        $this->unique[] = [$columns, $options];
    }

    /**
     * 添加外键索引
     * @param $columns
     * @param $referenced_table
     * @param array $referenced_columns
     * @param array $options
     * @return void
     */
    public function addForeignKey($columns, $referenced_table, array $referenced_columns = ['id'], array $options = []): void
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $this->foreign[] = [$columns, $referenced_table, $referenced_columns, $options];
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function uuid(string $column, string $comment = null): Column
    {
        return $this->addColumn('uuid', $column, compact('comment'));
    }

    /**
     * 枚举
     * @param string $column
     * @param array $values
     * @return Column
     */
    public function enum(string $column, array $values): Column
    {
        return $this->addColumn('enum', $column, compact('values'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param bool $increment
     * @return Column
     */
    public function unsignedTinyInteger(string $column, string $comment = null, bool $increment = false): Column
    {
        return $this->tinyInteger($column, $comment, false, $increment);
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param bool $increment
     * @return Column
     */
    public function unsignedSmallInteger(string $column, string $comment = null, bool $increment = false): Column
    {
        return $this->smallInteger($column, $comment, false, $increment);
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param bool $increment
     * @return Column
     */
    public function unsignedMediumInteger(string $column, string $comment = null, bool $increment = false): Column
    {
        return $this->mediumInteger($column, $comment, false, $increment);
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param bool $increment
     * @return Column
     */
    public function unsignedInteger(string $column, string $comment = null, bool $increment = false): Column
    {
        return $this->integer($column, $comment, false, $increment);
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param bool $increment
     * @return Column
     */
    public function unsignedBigInteger(string $column, string $comment = null, bool $increment = false): Column
    {
        return $this->bigInteger($column, $comment, false, $increment);
    }

    /**
     * 添加 tinyint 自增列
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function tinyIncrements(string $column = 'id', string $comment = null): Column
    {
        return $this->unsignedTinyInteger($column, $comment, true);
    }

    /**
     * 添加 smallint 自增列
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function smallIncrements(string $column = 'id', string $comment = null): Column
    {
        return $this->unsignedSmallInteger($column, $comment, true);
    }

    /**
     * 添加 mediumint 自增列
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function mediumIncrements(string $column = 'id', string $comment = null): Column
    {
        return $this->unsignedMediumInteger($column, $comment, true);
    }

    /**
     * 添加自增列
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function integerIncrements(string $column = 'id', string $comment = null): Column
    {
        return $this->unsignedInteger($column, $comment, true);
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function bigIncrements(string $column = 'id', string $comment = null): Column
    {
        return $this->unsignedBigInteger($column, $comment, true);
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param bool $signed
     * @param bool $increment
     * @return Column
     */
    public function integer(string $column, string $comment = null, bool $signed = true, bool $increment = false): Column
    {

        $limit = MysqlAdapter::INT_REGULAR;

        return $this->addColumn('integer', $column, compact('comment', 'increment', 'signed', 'limit'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param bool $signed
     * @param bool $increment
     * @return Column
     */
    public function tinyInteger(string $column, string $comment = null, bool $signed = true, bool $increment = false): Column
    {
        $limit = MysqlAdapter::INT_TINY;

        return $this->addColumn('integer', $column, compact('comment', 'increment', 'signed', 'limit'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param bool $signed
     * @param bool $increment
     * @return Column
     */
    public function smallInteger(string $column, string $comment = null, bool $signed = true, bool $increment = false): Column
    {
        $limit = MysqlAdapter::INT_SMALL;

        return $this->addColumn('integer', $column, compact('comment', 'increment', 'signed', 'limit'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param bool $signed
     * @param bool $increment
     * @return Column
     */
    public function mediumInteger(string $column, string $comment = null, bool $signed = true, bool $increment = false): Column
    {
        $limit = MysqlAdapter::INT_MEDIUM;

        return $this->addColumn('integer', $column, compact('comment', 'increment', 'signed', 'limit'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param bool $signed
     * @param bool $increment
     * @return Column
     */
    public function bigInteger(string $column, string $comment = null, bool $signed = true, bool $increment = false): Column
    {
        $limit = MysqlAdapter::INT_BIG;

        return $this->addColumn('biginteger', $column, compact('increment', 'signed', 'limit', 'comment'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param null $length
     * @return Column
     */
    public function char(string $column, string $comment = null, $length = null): Column
    {
        $length = $length ?: 255;

        return $this->addColumn('char', $column, compact('length', 'comment'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param null $length
     * @return Column
     */
    public function string(string $column, string $comment = null, $length = null): Column
    {
        $length = $length ?: 255;

        return $this->addColumn('string', $column, compact('length', 'comment'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function text(string $column, string $comment = null): Column
    {
        return $this->addColumn('text', $column, compact('comment'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function tinyText(string $column, string $comment = null): Column
    {
        $limit = MysqlAdapter::TEXT_TINY;

        return $this->addColumn('text', $column, compact('limit', 'comment'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function mediumText(string $column, string $comment = null): Column
    {
        $limit = MysqlAdapter::TEXT_MEDIUM;

        return $this->addColumn('text', $column, compact('limit', 'comment'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function longText(string $column, string $comment = null): Column
    {
        $limit = MysqlAdapter::TEXT_LONG;

        return $this->addColumn('text', $column, compact('limit', 'comment'));
    }

    /**
     * 创建 JSON 列（MySQL 5.7.8+ 原生支持）
     *
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function json(string $column, string $comment = null): Column
    {
        return $this->addColumn('json', $column, compact('comment'));
    }

    /**
     * 创建 BLOB 列（二进制大对象，需要更大容量可链式调用 length() 指定）
     *
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function blob(string $column, string $comment = null): Column
    {
        return $this->addColumn('blob', $column, compact('comment'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param int $precision
     * @param int $scale
     * @param bool $signed
     * @return Column
     */
    public function float(string $column, string $comment = null, int $precision = 8, int $scale = 2, bool $signed = true): Column
    {
        return $this->addColumn('float', $column, compact('comment', 'precision', 'scale', 'signed'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param int $precision
     * @param int $scale
     * @return Column
     */
    public function floatUnsigned(string $column, string $comment = null, int $precision = 8, int $scale = 2): Column
    {
        return $this->float($column, $comment, $precision, $scale, false);
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param integer|null $precision
     * @param integer|null $scale
     * @param bool $signed
     * @return Column
     */
    public function double(string $column, string $comment = null, int $precision = null, int $scale = null, bool $signed = true): Column
    {
        return $this->addColumn('double', $column, compact('comment', 'precision', 'scale', 'signed'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param integer|null $precision
     * @param integer|null $scale
     * @return Column
     */
    public function doubleUnsigned(string $column, string $comment = null, int $precision = null, int $scale = null): Column
    {
        return $this->double($column, $comment, $precision, $scale, false);
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param integer $precision
     * @param integer $scale
     * @param bool $signed
     * @return Column
     */
    public function decimal(string $column, string $comment = null, int $precision = 10, int $scale = 2, bool $signed = true): Column
    {
        return $this->addColumn('decimal', $column, compact('comment', 'precision', 'scale', 'signed'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param int $precision
     * @param int $scale
     * @return Column
     */
    public function decimalUnsigned(string $column, string $comment = null, int $precision = 10, int $scale = 2): Column
    {
        return $this->decimal($column, $comment, $precision, $scale, false);
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function boolean(string $column, string $comment = null): Column
    {
        return $this->addColumn('boolean', $column, compact('comment'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function date(string $column, string $comment = null): Column
    {
        return $this->addColumn('date', $column, compact('comment'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param int $precision 小数秒精度 0-6，> 0 时写入列定义
     * @param bool $null
     * @return Column
     */
    public function dateTime(string $column, string $comment = null, int $precision = 0, bool $null = false): Column
    {
        $options = compact('comment', 'null');

        if ($precision > 0) {
            $options['precision'] = $precision;
        }

        return $this->addColumn('datetime', $column, $options);
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param int $precision 小数秒精度 0-6，> 0 时写入列定义
     * @return Column
     */
    public function time(string $column, string $comment = null, int $precision = 0): Column
    {
        $options = compact('comment');

        if ($precision > 0) {
            $options['precision'] = $precision;
        }

        return $this->addColumn('time', $column, $options);
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param int $precision 小数秒精度 0-6，> 0 时写入列定义
     * @return Column
     */
    public function timestamp(string $column, string $comment = null, int $precision = 0): Column
    {
        $options = compact('comment');

        if ($precision > 0) {
            $options['precision'] = $precision;
        }

        return $this->addColumn('timestamp', $column, $options);
    }

    /**
     * 创建 YEAR 列（年份，Phinx 固定生成 YEAR(4)）
     *
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function year(string $column, string $comment = null): Column
    {
        return $this->addColumn('year', $column, compact('comment'));
    }

    /**
     * 创建 geometry 类型的空间列（可存放任意几何值）
     *
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function geometry(string $column, string $comment = null): Column
    {
        return $this->addColumn('geometry', $column, compact('comment'));
    }

    /**
     * 创建 point 类型的空间列（点，坐标顺序 x=经度、y=纬度）
     *
     * @param string $column
     * @param string|null $comment
     * @param int|null $srid 空间参考系 ID，仅 MySQL 8.0+ 生效；不传则不写入该选项
     * @return Column
     */
    public function point(string $column, string $comment = null, int $srid = null): Column
    {
        $options = compact('comment');

        // Phinx 的 setSrid(int) 不接受 null，未指定坐标系时不能把该选项传给列定义
        if ($srid !== null) {
            $options['srid'] = $srid;
        }

        return $this->addColumn('point', $column, $options);
    }

    /**
     * 创建 linestring 类型的空间列（线）
     *
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function lineString(string $column, string $comment = null): Column
    {
        return $this->addColumn('linestring', $column, compact('comment'));
    }

    /**
     * 创建 polygon 类型的空间列（面）
     *
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function polygon(string $column, string $comment = null): Column
    {
        return $this->addColumn('polygon', $column, compact('comment'));
    }

    /**
     * 归一索引选项：字符串按索引名处理，未指定名字时用列名下划线拼接
     * @param array $columns 已数组化的列名
     * @param array|string|null $options 数组为索引选项，字符串为索引名
     * @return array
     */
    protected function normalizeIndexOptions(array $columns, $options = null): array
    {
        if (is_string($options)) {
            $options = ['name' => $options];
        } elseif ($options === null) {
            $options = [];
        } elseif (!is_array($options)) {
            throw new \InvalidArgumentException('索引选项只接受数组或字符串（字符串按索引名处理）');
        }

        if (empty($options['name'])) {
            $options['name'] = implode('_', $columns);
        }

        return $options;
    }
}