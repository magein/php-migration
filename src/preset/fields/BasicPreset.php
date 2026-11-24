<?php

namespace magein\migration\preset\fields;

use magein\migration\Column;
use magein\migration\preset\PresetGroup;

class BasicPreset extends PresetGroup
{

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function name(string $comment = null, int $length = 60): Column
    {
        $comment = $comment ?: '名称';

        return $this->builder->string('name', $comment, $length);
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
    public function status(string $comment = null, int $default = 1): Column
    {
        $comment = $comment ?: '状态 0 禁用 forbid 1 启用 open';

        return $this->builder->tinyInteger('status', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function sort(string $comment = null, int $default = 99): Column
    {
        $comment = $comment ?: '排序 正序';

        return $this->builder->smallInteger('sort', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function type(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '类型';

        return $this->builder->tinyInteger('type', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function alias(string $comment = null, int $length = 15): Column
    {
        $comment = $comment ?: '别名';

        return $this->builder->string('alias', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function reason(string $comment = null): Column
    {
        $comment = $comment ?: '原因';

        return $this->builder->string('reason', $comment)->default('');
    }
}
