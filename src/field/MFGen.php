<?php

namespace magein\migration\field;

use magein\migration\Column;

class MFGen extends MFBase
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
     * @param int $length
     * @return Column
     */
    public function alias(string $comment = null, int $length = 15): Column
    {
        $comment = $comment ?: '别名';

        return $this->builder->string('alias', $comment, $length);
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

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function tel(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '联系电话';

        return $this->builder->string('contact', $comment, $length)->default('');
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
    public function parentId(string $comment = null): Column
    {
        $comment = $comment ?: '上级分类';

        return $this->builder->integer('parent_id', $comment)->default(0);
    }

    /**
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
    public function scene(string $comment = null, int $default = 1): Column
    {
        $comment = $comment ?: '使用场景';

        return $this->builder->integerTiny('scene', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param string $default
     * @return Column
     */
    public function sceneMultiple(string $comment = null, string $default = ''): Column
    {
        $comment = $comment ?: '使用场景';

        return $this->builder->string('scene', $comment)->default($default);
    }

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
     * @param int $length
     * @return Column
     */
    public function scale(string $comment = null, int $length = 140): Column
    {
        $comment = $comment ?: '规模';

        return $this->builder->string('scale', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function from(string $comment = null, int $default = 1): Column
    {
        $comment = $comment ?: '来源';

        return $this->builder->integerTiny('from', $comment)->default($default);
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
     * @param string|null $comment
     * @return Column
     */
    public function industry(string $comment = null): Column
    {
        $comment = $comment ?: '行业';

        return $this->builder->integer('industry', $comment)->default(0);
    }

    /**
     * @param string|null $comment
     * @param string $default
     * @return Column
     */
    public function attribute(string $comment = null, string $default = ''): Column
    {
        $comment = $comment ?: '属性';

        return $this->builder->string('attribute', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function tag(string $comment = null): Column
    {
        $comment = $comment ?: '简介';

        return $this->builder->string('tag', $comment)->default('');
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
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function action(string $comment = null, int $default = 1): Column
    {
        $comment = $comment ?: '行为';

        return $this->builder->integerTiny('action', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function level(string $comment = null, int $default = 1): Column
    {
        $comment = $comment ?: '等级';

        return $this->builder->integerTiny('level', $comment)->default($default);
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
     * @param $comment
     * @return Column
     */
    public function auditedAt($comment = null): Column
    {
        $comment = $comment ?: '审核时间';

        return $this->builder->dateTime('audited_at', $comment)->nullable();
    }

    /**
     * @param $comment
     * @return Column
     */
    public function expiredAt($comment = null): Column
    {
        $comment = $comment ?: '过期时间';

        return $this->builder->dateTime('expired_at', $comment)->nullable();
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function money(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '金额 单位分';

        return $this->builder->integer('money', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function balance(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '余额 单位分';

        return $this->builder->integer('balance', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function price(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '价格 单位分';

        return $this->builder->integer('price', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function num(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '数量';

        return $this->builder->integer('num', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function stock(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '库存';

        return $this->builder->integer('stock', $comment)->default($default);
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

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function purpose(string $comment = null, int $default = 1): Column
    {
        $comment = $comment ?: '目的';

        return $this->builder->integer('purpose', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function department(string $comment = null): Column
    {
        $comment = $comment ?: '部门';

        return $this->builder->integer('department', $comment)->default(0);
    }

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

        return $this->builder->string('icon', $comment, 60)->default('');
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
     * @param string|null $comment
     * @return Column
     */
    public function value(string $comment = null): Column
    {
        $comment = $comment ?: '值';

        return $this->builder->string('value', $comment)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function code(string $comment = null, int $length = 12): Column
    {
        $comment = $comment ?: '代码';

        return $this->builder->string('code', $comment, $length)->unique();
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

        return $this->builder->integer('width', $comment)->default(0);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function weight(string $comment = null): Column
    {
        $comment = $comment ?: '克重';

        return $this->builder->integer('width', $comment)->default(0);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function version(string $comment = null): Column
    {
        $comment = $comment ?: '版本';

        return $this->builder->string('version', $comment, 60)->default('');
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function device(string $comment = null): Column
    {
        $comment = $comment ?: '设备';

        return $this->builder->string('device', $comment, 60)->default('');
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function deviceId(string $comment = null): Column
    {
        $comment = $comment ?: '设备';

        return $this->builder->integerTiny('device_id', $comment);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function platform(string $comment = null): Column
    {
        $comment = $comment ?: '平台';

        return $this->builder->string('platform', $comment, 60)->default('');
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function platformId(string $comment = null): Column
    {
        $comment = $comment ?: '平台';

        return $this->builder->integerTiny('platform_id', $comment);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function group(string $comment = null): Column
    {
        $comment = $comment ?: '分组';

        return $this->builder->string('group', $comment)->default('');
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function groupId(string $comment = null): Column
    {
        $comment = $comment ?: '分组';

        return $this->builder->integerTiny('group_id', $comment);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function url(string $comment = null): Column
    {
        $comment = $comment ?: '地址';

        return $this->builder->string('url', $comment)->default('');
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function redirect(string $comment = null): Column
    {
        $comment = $comment ?: '跳转地址';

        return $this->builder->string('url', $comment)->default('');
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

    /**
     * @param string|null $comment
     * @return Column
     */
    public function ip(string $comment = null): Column
    {
        $comment = $comment ?: 'ip';

        return $this->builder->string('reason', $comment)->default('');
    }
}