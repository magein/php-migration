<?php

namespace magein\migration;

use magein\migration\field\MFAddress;
use magein\migration\field\MFGeneral;
use magein\migration\field\MFUser;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * @property MFUser user
 * @property MFGeneral gen
 * @property MFAddress addr
 */
class Builder
{
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
     * @var string|float|int
     */
    protected string|int|float $version = '';

    /**
     *  可选值：
     *   comment     给表结构设置文本注释
     *   row_format  设置行记录模格式
     *   engine      表引擎 (默认 InnoDB)
     *   collation    表字符集
     *   signed       是否无符号 signed(默认 true)
     * @var array
     */
    protected array $options = [];

    protected static int $defaultStringLength = 255;

    /**
     * 添加的列
     * @var array
     */
    protected array $columns = [];

    public function __construct($options = null)
    {
        if (is_string($options)) {
            $this->options['comment'] = $options;
        }

        if (!isset($options['id'])) {
            $this->options['id'] = false;
        }

        if (empty($this->options['engine'])) {
            $this->setEngine(($config['engine'] ?? '') ?: 'MyISAM');
        }

        if (empty($this->options['collation'])) {
            $this->setCollation(($config['collation'] ?? '') ?: 'utf8_unicode_ci');
        }
    }

    /**
     * 设置主键
     * @param $value
     * @return void
     */
    public function setPrimaryKey($value = null): void
    {
        if (is_null($value)) {
            $this->options['id'] = false;
            unset($this->options['primary_key']);
        } else {
            $this->options['id'] = true;
            $this->options['primary_key'] = $value;
        }
    }

    /**
     * @param $engine
     */
    public function setEngine($engine): void
    {
        $this->options['engine'] = $engine;
    }

    /**
     * 设置表注释
     * @param $comment
     */
    public function setComment($comment): void
    {
        $this->options['comment'] = $comment;
    }

    /**
     * 设置排序比对方法
     * @param $collation
     */
    public function setCollation($collation): void
    {
        $this->options['collation'] = $collation;
    }

    /**
     * 设置InnoDB引擎
     * @return void
     */
    public function innoDB(): void
    {
        $this->options['engine'] = 'InnoDB';
    }

    /**
     * 设置MyISAM引擎
     * @return void
     */
    public function myISAM(): void
    {
        $this->options['engine'] = 'MyISAM';
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * 设置版本
     * @param float|string|$version
     * @return void
     */
    public function setVersion(float|string $version): void
    {
        if ($version) {
            $this->version = $version;
        }
    }

    /**
     * @return float|int|string
     */
    public function getVersion(): float|int|string
    {
        return $this->version;
    }

    public function __get($name)
    {
        if ($name === 'user') {
            return new MFUser($this);
        }

        if ($name === 'gen') {
            return new MFGeneral($this);
        }

        if ($name === 'addr') {
            return new MFAddress($this);
        }
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

        $this->columns[$name] = $column;

        return $column;
    }

    /**
     * 添加索引或者组合索引
     * @param array|string $columns
     * @param $options
     * @return void
     */
    public function addIndex(array|string $columns, $options = null): void
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $options = $this->indexOptions($columns, $options);

        $this->index[] = [$columns, $options];
    }

    /**
     * 添加唯一索引
     * @param array|string $columns
     * @param $options
     * @return void
     */
    public function addUniqueKey(array|string $columns, $options = null): void
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $options = $this->indexOptions($columns, $options);
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

    public function id(): void
    {
        $this->options['id'] = true;
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
     * @return Column
     */
    public function bigIncrements(string $column = 'id', string $comment = null): Column
    {
        return $this->unsignedBigInteger($column, $comment, true);
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
     * @param bool $increment
     * @return Column
     */
    public function unsignedInteger(string $column, string $comment = null, bool $increment = false): Column
    {
        return $this->integer($column, $comment, false, $increment);
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
    public function integerTiny(string $column, string $comment = null, bool $signed = true, bool $increment = false): Column
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
    public function integerSmall(string $column, string $comment = null, bool $signed = true, bool $increment = false): Column
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
    public function integerMedium(string $column, string $comment = null, bool $signed = true, bool $increment = false): Column
    {
        $limit = MysqlAdapter::INT_MEDIUM;

        return $this->addColumn('integer', $column, compact('comment', 'increment', 'signed', 'limit'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param null $length
     * @return Column
     */
    public function char(string $column, string $comment = null, $length = null): Column
    {
        $length = $length ?: self::$defaultStringLength;

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
        $length = $length ?: self::$defaultStringLength;

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
    public function textTiny(string $column, string $comment = null): Column
    {
        $limit = MysqlAdapter::TEXT_TINY;

        return $this->addColumn('text', $column, compact('limit', 'comment'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function textMedium(string $column, string $comment = null): Column
    {
        $limit = MysqlAdapter::TEXT_MEDIUM;

        return $this->addColumn('text', $column, compact('limit', 'comment'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function textLong(string $column, string $comment = null): Column
    {
        $limit = MysqlAdapter::TEXT_LONG;

        return $this->addColumn('text', $column, compact('limit', 'comment'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param int $precision
     * @param int $scale
     * @param bool $signed
     * @return Column
     */
    public function float(string $column, string $comment = null, int $precision = 8, int $scale = 2, bool $signed = false): Column
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
        return $this->float($column, $comment, $precision, $scale, true);
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param integer|null $precision
     * @param integer|null $scale
     * @param bool $signed
     * @return Column
     */
    public function double(string $column, string $comment = null, int $precision = null, int $scale = null, bool $signed = false): Column
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
        return $this->double($column, $comment, $precision, $scale, true);
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param integer $precision
     * @param integer $scale
     * @param bool $signed
     * @return Column
     */
    public function decimal(string $column, string $comment = null, int $precision = 10, int $scale = 2, bool $signed = false): Column
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
        return $this->decimal($column, $comment, $precision, $scale, true);
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
     * @param int $precision
     * @param bool $null
     * @return Column
     */
    public function dateTime(string $column, string $comment = null, int $precision = 0, bool $null = false): Column
    {
        if ($this->version < 5.6) {
            $options = compact('comment', 'null');
        } else {
            $options = compact('comment', 'precision', 'null');
        }

        return $this->addColumn('datetime', $column, $options);
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param int $precision
     * @return Column
     */
    public function time(string $column, string $comment = null, int $precision = 0): Column
    {
        return $this->addColumn('time', $column, compact('comment', 'precision'));
    }

    /**
     * @param string $column
     * @param string|null $comment
     * @param int $precision
     * @return Column
     */
    public function timestamp(string $column, string $comment = null, int $precision = 0): Column
    {
        return $this->addColumn('timestamp', $column, compact('comment', 'precision'));
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
     * @param int $precision
     * @return Column
     */
    public function createdAt(int $precision = 0): Column
    {
        return $this->dateTime('created_at', '创建时间', $precision);
    }

    /**
     * @param int $precision
     * @return Column
     */
    public function updatedAt(int $precision = 0): Column
    {
        return $this->dateTime('updated_at', '更新时间', $precision);
    }

    /**
     * @param int $precision
     * @return Column
     */
    public function deletedAt(int $precision = 0): Column
    {
        return $this->dateTime('deleted_at', '删除时间', $precision, true);
    }

    /**
     * Create a new geometry column on the table.
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
     * Create a new point column on the table.
     *
     * @param string $column
     * @param int|null $srid
     * @return Column
     */
    public function point(string $column, int $srid = null): Column
    {
        return $this->addColumn('point', $column, compact('srid'));
    }

    /**
     * Create a new linestring column on the table.
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
     * Create a new polygon column on the table.
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
     * Create a new geometrycollection column on the table.
     *
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function geometryCollection(string $column, string $comment = null): Column
    {
        return $this->addColumn('geometrycollection', $column, compact('comment'));
    }

    /**
     * Create a new multipoint column on the table.
     *
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function multiPoint(string $column, string $comment = null): Column
    {
        return $this->addColumn('multipoint', $column, compact('comment'));
    }

    /**
     * Create a new multilinestring column on the table.
     *
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function multiLineString(string $column, string $comment = null): Column
    {
        return $this->addColumn('multilinestring', $column, compact('comment'));
    }

    /**
     * Create a new multipolygon column on the table.
     *
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function multiPolygon(string $column, string $comment = null): Column
    {
        return $this->addColumn('multipolygon', $column, compact('comment'));
    }

    /**
     * Create a new multipolygon column on the table.
     *
     * @param string $column
     * @param string|null $comment
     * @return Column
     */
    public function multiPolygonZ(string $column, string $comment = null): Column
    {
        return $this->addColumn('multipolygonz', $column, compact('comment'));
    }

    /**
     * 从命名
     * @param $name
     * @param $new_name
     * @return Column
     */
    public function rename($name, $new_name): Column
    {
        $column = new Column();
        $column->name($name);
        $column->rename($name, $new_name);
        $this->columns[$name] = $column;
        return $column;
    }

    /**
     * @param $name
     * @param $type
     * @param string|null $comment
     * @return Column
     */
    public function change($name, $type, string $comment = null): Column
    {
        $column = new Column();
        $column->name($name);
        $column->type($type);
        $column->comment($comment ?: '');
        $column->change();
        $this->columns[$name] = $column;
        return $column;
    }

    /**
     * 请不要再change方法中调用，如果在change方法中调用，将被舍弃
     * @param $name
     * @return Column
     */
    public function remove($name): Column
    {
        $column = new Column();
        $column->name($name);
        $column->remove();
        $this->columns[$name] = $column;
        return $column;
    }

    /**
     * @param array|string $columns
     * @param null $options
     * @return array
     */
    protected function indexOptions(array|string $columns, $options = null): array
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        if (is_null($options)) {
            $options = [];
        } elseif (is_string($options)) {
            $name = $options;
            $options = [];
            $options['name'] = $name;
        }

        if (empty($options['name'] ?? '')) {
            $options['name'] = implode('_', $columns);
        }

        return $options;
    }
}