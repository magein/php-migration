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
    protected $name = '';

    /**
     * 字段类型
     * @var string
     */
    protected $type = '';

    /**
     * 注释
     * @var string
     */
    protected $comment = '';

    /**
     * 长度
     * @var int
     */
    protected $length = 0;

    /**
     * 在某个字段之后
     * @var string
     */
    protected $after = '';

    /**
     * 默认值
     * @var string|int|null
     */
    protected $default = -1;

    /**
     * 允许null
     * @var int
     */
    protected $null = false;

    /**
     * 其他
     * @var array
     */
    protected $others = [];

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
     * 重命名字段
     * @var array
     */
    protected $rename = [];

    /**
     * 删除行
     * @var bool
     */
    protected $remove = false;

    /**
     * 更新
     * @var bool
     */
    protected $change = false;

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
     * @param string|int $value
     * @return $this
     */
    public function default($value = ''): Column
    {
        $this->default = $value;

        return $this;
    }

    /**
     * @param $after
     * @return $this
     */
    public function after($after): Column
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
     * @param string $index_name
     * @return $this
     */
    public function index(string $index_name = ''): Column
    {
        $this->index = $index_name ?: $this->name;

        return $this;
    }

    /**
     * @param string $index_name
     * @return $this
     */
    public function unique(string $index_name = ''): Column
    {
        $this->unique = $index_name ?: $this->name;

        /**
         * 设置索引有长度要求
         */
        if ($this->type === 'string' && $this->length > 191) {
            $this->length = 191;
        }

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
     * @return int|string|null
     */
    public function getDefault()
    {
        return $this->default;
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
     * @param $name
     * @param $new_name
     * @return $this
     */
    public function rename($name, $new_name): Column
    {
        if ($name && $new_name) {
            $this->rename = [$name, $new_name];
        }

        return $this;
    }

    /**
     * @return array
     */
    public function isRename(): array
    {
        return is_array($this->rename) ? array_filter($this->rename) : [];
    }

    /**
     * @param bool $remove
     * @return void
     */
    public function remove(bool $remove = true)
    {
        $this->remove = $remove;
    }

    /**
     * 是否删除
     * @return bool
     */
    public function isRemove(): bool
    {
        return boolval($this->remove);
    }

    /**
     * @param bool $change
     * @return void
     */
    public function change(bool $change = true)
    {
        $this->change = $change;
    }

    /**
     * @return bool
     */
    public function isChange(): bool
    {
        return boolval($this->change);
    }
}