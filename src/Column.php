<?php

namespace magein\migration;

/**
 * 数据库字段行
 *
 * 名称、类型、描述、长度等等
 */
class Column
{

    /**
     * 字段名称
     * @var string
     */
    protected string $name = '';

    /**
     * 字段类型
     * @var string
     */
    protected string $type = '';

    /**
     * 注释
     * @var string
     */
    protected string $comment = '';

    /**
     * 长度
     * @var int
     */
    protected int $length = 0;

    /**
     * 在某个字段之后
     * @var string
     */
    protected string $after = '';

    /**
     * 默认值
     * @var mixed
     */
    protected $default = null;

    /**
     * 是否显式声明过默认值（-1 也是合法默认值，不能用具体值当哨兵）
     * @var bool
     */
    protected bool $has_default = false;

    /**
     * 允许null
     * @var int
     */
    protected $null = false;

    /**
     * 其他
     * @var array
     */
    protected array $others = [];

    /**
     * 常规索引
     * @var array|string
     */
    protected $index = [];

    /**
     * 唯一索引
     * @var array|string
     */
    protected $unique = [];

    /**
     * 更新已有列的定义
     * @var bool
     */
    protected bool $change = false;

    /**
     * @param $type
     * @return $this
     */
    public function type($type): Column
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param $name
     * @return $this
     */
    public function name($name): Column
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param $comment
     * @return $this
     */
    public function comment($comment): Column
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * @param $len
     * @return $this
     */
    public function length($len): Column
    {
        $this->length = intval($len);

        return $this;
    }

    /**
     * 小数精度（decimal / float / double 才有意义）
     * @param int $precision
     * @return $this
     */
    public function precision(int $precision): Column
    {
        $this->others['precision'] = $precision;

        return $this;
    }

    /**
     * 小数位数（decimal / float / double 才有意义）
     * @param int $scale
     * @return $this
     */
    public function scale(int $scale): Column
    {
        $this->others['scale'] = $scale;

        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function default($value = ''): Column
    {
        $this->default = $value;
        $this->has_default = true;

        return $this;
    }

    /**
     * @param string $after
     * @return $this
     */
    public function after(string $after): Column
    {
        $this->after = $after;

        return $this;
    }

    /**
     * @param array $others
     * @return $this
     */
    public function others(array $others): Column
    {
        $this->others = $others;

        return $this;
    }

    /**
     * @return $this
     */
    public function nullable(): Column
    {
        $this->null = true;

        return $this;
    }

    /**
     * 标记该列需要建普通索引
     * @param string $index_name 索引名，不传则为 idx_{列名}
     * @return $this
     */
    public function index(string $index_name = ''): Column
    {
        $this->index = $index_name ?: 'idx_' . $this->name;

        return $this;
    }

    /**
     * 标记该列需要建唯一索引，只登记索引，不改列长度。
     * 191 是 utf8mb4 + COMPACT/REDUNDANT 行格式下 767 字节索引前缀的旧限制；
     * MySQL 5.7 默认 DYNAMIC + innodb_large_prefix=ON（上限 3072 字节），
     * VARCHAR(255) 可正常建唯一索引；旧环境自行 ->length(191) 或调整行格式。
     *
     * @param string $index_name 索引名，不传则为 uniq_{列名}
     * @return $this
     */
    public function unique(string $index_name = ''): Column
    {
        $this->unique = $index_name ?: 'uniq_' . $this->name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @return int
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * @return string
     */
    public function getAfter(): string
    {
        return $this->after;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @return bool
     */
    public function hasDefault(): bool
    {
        return $this->has_default;
    }

    /**
     * @return bool|int
     */
    public function getNull()
    {
        return $this->null;
    }

    /**
     * @return array
     */
    public function getOthers(): array
    {
        return $this->others;
    }

    /**
     * @return array|string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * @return array|string
     */
    public function getUnique()
    {
        return $this->unique;
    }

    /**
     * @param bool $change
     * @return void
     */
    public function change(bool $change = true): void
    {
        $this->change = $change;
    }

    /**
     * @return bool
     */
    public function isChange(): bool
    {
        return $this->change;
    }
}