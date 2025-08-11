<?php

namespace magein\migration\field;

use magein\migration\Builder;
use magein\migration\Column;

class MFGeneral
{

    protected Builder $builder;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function status(string $comment = null, int $default = 1): Column
    {
        $comment = $comment ?: '状态 0 禁用 forbid 1 启用 open';

        return $this->builder->integerTiny('status', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function form(string $comment = null, int $default = 1): Column
    {
        $comment = $comment ?: '来源';

        return $this->builder->integerTiny('form', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function channel(string $comment = null, int $default = 1): Column
    {
        $comment = $comment ?: '渠道';

        return $this->builder->integerTiny('channel', $comment)->default($default);
    }

    /**
     * 排序字段
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function sort(string $comment = null, int $default = 99): Column
    {
        $comment = $comment ?: '排序 正序';

        return $this->builder->integerSmall('sort', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function title(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '标题';

        return $this->builder->string('title', $comment, $length);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function content(string $comment = null): Column
    {
        $comment = $comment ?: '内容';

        return $this->builder->text('content', $comment)->nullable();
    }

    /**
     * @param string|null $comment
     * @param string $default
     * @return Column
     */
    public function remark(string $comment = null, string $default = ''): Column
    {
        $comment = $comment ?: '备注';

        return $this->builder->string('remark', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function type(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '类型';

        return $this->builder->integerTiny('type', $comment)->default($default);
    }

    /**
     * @param $comment
     * @return Column
     */
    public function beginTime($comment = null): Column
    {
        $comment = $comment ?: '开始时间';

        return $this->builder->dateTime('begin_time', $comment);
    }

    /**
     * @param $comment
     * @return Column
     */
    public function startTime($comment = null): Column
    {
        $comment = $comment ?: '开始时间';

        return $this->builder->dateTime('start_time', $comment);
    }

    /**
     * @param $comment
     * @return Column
     */
    public function endTime($comment = null): Column
    {
        $comment = $comment ?: '结束时间';

        return $this->builder->dateTime('end_time', $comment);
    }

    /**
     * @return void
     */
    public function _statusSort()
    {
        $this->status();
        $this->sort();
    }

    /**
     * @return void
     */
    public function _datetime()
    {
        $this->beginTime();
        $this->endTime();
    }
}