<?php

namespace magein\migration\preset\fields;

use magein\migration\Column;
use magein\migration\preset\PresetGroup;

class ArticlePreset extends PresetGroup
{

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
     * @param int $length
     * @return Column
     */
    public function description(string $comment = null, int $length = 140): Column
    {
        $comment = $comment ?: '描述';

        return $this->builder->string('description', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function intro(string $comment = null, int $length = 140): Column
    {
        $comment = $comment ?: '简介';

        return $this->builder->string('intro', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function author(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '作者';

        return $this->builder->string('author', $comment, $length)->default('');
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
     * @return Column
     */
    public function tag(string $comment = null): Column
    {
        $comment = $comment ?: '标签';

        return $this->builder->string('tag', $comment)->default('');
    }
}
