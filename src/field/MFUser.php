<?php

namespace magein\migration\field;

use magein\migration\Column;

class MFUser extends MFBase
{

    /**
     * 账号
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function username(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '登录账号';

        return $this->builder->string('username', $comment, $length)->unique();
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function nickname(string $comment = null, int $length = 60): Column
    {
        $comment = $comment ?: '昵称';

        return $this->builder->string('nickname', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function avatar(string $comment = null, int $length = 255): Column
    {
        $comment = $comment ?: '头像';

        return $this->builder->string('avatar', $comment, $length)->default('');
    }

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
     * 账号
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function account(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '登录账号';

        return $this->builder->string('account', $comment, $length);
    }

    /**
     * 密码
     * @param string|null $comment
     * @return Column
     */
    public function password(string $comment = null): Column
    {
        $comment = $comment ?: '密码';

        return $this->builder->string('password', $comment);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function age(string $comment = null): Column
    {
        $comment = $comment ?: '年龄';

        return $this->builder->integerTiny('age', $comment);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function birthday(string $comment = null): Column
    {
        $comment = $comment ?: '出生年月';

        return $this->builder->date('birthday', $comment)->nullable();
    }

    /**
     * 性别
     * @param string|null $comment
     * @param string|int $default
     * @return Column
     */
    public function gender(string $comment = null, $default = 0): Column
    {
        $comment = $comment ?: '性别 0 未知 secret 1 男 man 2 女 woman';

        $default = $default . '';

        return $this->builder->enum('gender', [0, 1, 2])->comment($comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function points(string $comment = null): Column
    {
        $comment = $comment ?: '积分';

        return $this->builder->integer('points', $comment)->default(0);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function score(string $comment = null): Column
    {
        $comment = $comment ?: '分数';

        return $this->builder->integer('score', $comment)->default(0);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function idType(string $comment = null, int $default = 1): Column
    {
        $comment = $comment ?: '证件类型 1 身份证 id 2 港澳通行证 hk_macao 3 tai_wan  4 护照 passport 99 其他 other';

        return $this->builder->integerTiny('id_type', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function idNumber(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '证件号码';

        return $this->builder->string('id_number', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function idFront(string $comment = null, int $length = 60): Column
    {
        $comment = $comment ?: '证件正面';

        return $this->builder->string('id_number', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function idBack(string $comment = null, int $length = 60): Column
    {
        $comment = $comment ?: '证件反面';

        return $this->builder->string('id_number', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function nativePlace(string $comment = null, int $length = 60): Column
    {
        $comment = $comment ?: '籍贯';

        return $this->builder->string('id_number', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function ethnic(string $comment = null): Column
    {
        $comment = $comment ?: '民族';

        return $this->builder->integerTiny('ethnic', $comment)->default(1);
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function ethnicStr(string $comment = null, int $length = 60): Column
    {
        $comment = $comment ?: '民族';

        return $this->builder->string('ethnic', $comment, $length)->default('汉族');
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function userId(string $comment = null): Column
    {
        $comment = $comment ?: '用户id';

        return $this->builder->unsignedInteger('user_id', $comment);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function memberId(string $comment = null): Column
    {
        $comment = $comment ?: '会员id';

        return $this->builder->unsignedInteger('member_id', $comment);
    }
}