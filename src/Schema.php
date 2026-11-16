<?php

namespace magein\migration;

/**
 * 表级选项：引擎、注释、字符集、主键策略等，构建动作在 Builder 上，这里只承载表本身的数据
 */
class Schema
{
    /**
     * 选项白名单：只透传 Phinx 能识别的键，其余键静默丢弃
     * @var array
     */
    private const OPTIONS = [
        'id', 'primary_key', 'comment', 'engine', 'collation', 'row_format', 'signed', 'limit',
    ];

    /**
     *   id           是否自动创建自增主键（默认 false）
     *   primary_key  主键列名或列名数组
     *   comment      表注释
     *   engine       表引擎（默认 InnoDB）
     *   collation    字符集排序规则（默认 utf8_unicode_ci）
     *   row_format   行记录格式
     *   signed       自增主键是否有符号
     *   limit        自增主键列的 limit
     * @var array
     */
    protected $options = [];

    /**
     * @param Schema|array|string|null $options Schema 实例原样返回，字符串按表注释处理，数组按白名单处理
     * @return Schema
     */
    public static function from($options = null): Schema
    {
        if ($options instanceof self) {
            return $options;
        }

        return new self($options);
    }

    /**
     * @param Schema|array|string|null $options
     */
    public function __construct($options = null)
    {
        if (is_string($options)) {
            $options = ['comment' => $options];
        }

        if (!is_array($options)) {
            $options = [];
        }

        foreach (self::OPTIONS as $key) {
            if (array_key_exists($key, $options)) {
                $this->options[$key] = $options[$key];
            }
        }

        // 未声明 id 时 Phinx 会自动补一个自增主键，这里默认关掉
        if (!array_key_exists('id', $this->options)) {
            $this->options['id'] = false;
        }

        if (empty($this->options['engine'])) {
            $this->options['engine'] = 'InnoDB';
        }

        if (empty($this->options['collation'])) {
            $this->options['collation'] = 'utf8_unicode_ci';
        }
    }

    /**
     * 设置主键，传 null 则关闭自增主键并清除 primary_key
     * @param string|array|null $value
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
     * 让 Phinx 自动创建自增主键 id
     * @return void
     */
    public function id(): void
    {
        $this->options['id'] = true;
    }

    /**
     * @param string $engine
     * @return void
     */
    public function setEngine(string $engine): void
    {
        $this->options['engine'] = $engine;
    }

    /**
     * @return void
     */
    public function setEngineInnodb(): void
    {
        $this->options['engine'] = 'InnoDB';
    }

    /**
     * @return void
     */
    public function setEngineMyISAM(): void
    {
        $this->options['engine'] = 'MyISAM';
    }

    /**
     * @param string $comment
     * @return void
     */
    public function setComment(string $comment): void
    {
        $this->options['comment'] = $comment;
    }

    /**
     * @param string $collation
     * @return void
     */
    public function setCollation(string $collation): void
    {
        $this->options['collation'] = $collation;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->options;
    }
}
