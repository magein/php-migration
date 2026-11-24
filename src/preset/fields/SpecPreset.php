<?php

namespace magein\migration\preset\fields;

use magein\migration\Column;
use magein\migration\preset\PresetGroup;

class SpecPreset extends PresetGroup
{

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function size(string $comment = null, int $length = 60): Column
    {
        $comment = $comment ?: '尺寸';

        return $this->builder->string('size', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function width(string $comment = null): Column
    {
        $comment = $comment ?: '宽度';

        return $this->builder->integer('width', $comment)->default(0);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function height(string $comment = null): Column
    {
        $comment = $comment ?: '高度';

        return $this->builder->integer('height', $comment)->default(0);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function weight(string $comment = null): Column
    {
        $comment = $comment ?: '克重';

        return $this->builder->integer('weight', $comment)->default(0);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function unit(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '单位';

        return $this->builder->integer('unit', $comment)->default($default);
    }
}
