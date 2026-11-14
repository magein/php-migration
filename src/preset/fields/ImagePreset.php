<?php

namespace magein\migration\preset\fields;

use magein\migration\Column;
use magein\migration\preset\PresetGroup;

class ImagePreset extends PresetGroup
{

    /**
     * @param string|null $comment
     * @return Column
     */
    public function thumb(string $comment = null): Column
    {
        $comment = $comment ?: '缩略图';

        return $this->builder->string('thumb', $comment)->default('');
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function logo(string $comment = null): Column
    {
        $comment = $comment ?: 'logo';

        return $this->builder->string('logo', $comment)->default('');
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function icon(string $comment = null): Column
    {
        $comment = $comment ?: '图标';

        return $this->builder->string('icon', $comment)->default('');
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function image(string $comment = null): Column
    {
        $comment = $comment ?: '图片';

        return $this->builder->string('image', $comment)->default('');
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function picture(string $comment = null): Column
    {
        $comment = $comment ?: '图片';

        return $this->builder->string('picture', $comment)->default('');
    }

    /**
     * 照片
     * @param string|null $comment
     * @return Column
     */
    public function photo(string $comment = null): Column
    {
        $comment = $comment ?: '照片';

        return $this->builder->string('photo', $comment)->default('');
    }

    /**
     * 封面图
     * @param string|null $comment
     * @return Column
     */
    public function cover(string $comment = null): Column
    {
        $comment = $comment ?: '封面图';

        return $this->builder->string('cover', $comment)->default('');
    }
}
