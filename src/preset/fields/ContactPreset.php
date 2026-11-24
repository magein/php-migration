<?php

namespace magein\migration\preset\fields;

use magein\migration\Column;
use magein\migration\preset\PresetGroup;

class ContactPreset extends PresetGroup
{

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function phone(string $comment = null, int $length = 11): Column
    {
        $comment = $comment ?: '手机号码';

        return $this->builder->char('phone', $comment, $length);
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function mobile(string $comment = null, int $length = 11): Column
    {
        $comment = $comment ?: '手机号码';

        return $this->builder->char('mobile', $comment, $length);
    }

    /**
     * 邮箱
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function email(string $comment = null, int $length = 60): Column
    {
        $comment = $comment ?: '邮箱地址';

        return $this->builder->string('email', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function tel(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '联系电话';

        return $this->builder->string('tel', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function contact(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '联系人';

        return $this->builder->string('contact', $comment, $length)->default('');
    }
}
