<?php

namespace magein\migration\field;

use magein\migration\Column;

class MFFile extends MFBase
{
    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function filename(string $comment = null, int $length = 150): Column
    {
        $comment = $comment ?: '文件名称';

        return $this->builder->string('filename', $comment, $length);
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function originName(string $comment = null, int $length = 150): Column
    {
        $comment = $comment ?: '原名称';

        return $this->builder->string('origin_name', $comment, $length);
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function filepath(string $comment = null, int $length = 255): Column
    {
        $comment = $comment ?: '文件路径';

        return $this->builder->string('filepath', $comment, $length);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function filesize(string $comment = null): Column
    {
        $comment = $comment ?: '文件大小';

        return $this->builder->integer('filesize', $comment);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function ext(string $comment = null): Column
    {
        $comment = $comment ?: '文件后缀';

        return $this->builder->integer('ext', $comment)->default(0);
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function mime(string $comment = null, int $length = 60): Column
    {
        $comment = $comment ?: 'mime类型';

        return $this->builder->string('mime', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function hash(string $comment = null, int $length = 255): Column
    {
        $comment = $comment ?: '哈希值';

        return $this->builder->string('hash', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function md5(string $comment = null, int $length = 255): Column
    {
        $comment = $comment ?: 'md5';

        return $this->builder->string('md5', $comment, $length)->default('');
    }
}