<?php

namespace magein\migration\preset\fields;

use magein\migration\Column;
use magein\migration\preset\PresetGroup;

class AddressPreset extends PresetGroup
{
    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function postcode(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '邮编';

        return $this->builder->string('postcode', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function province(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '省份';

        return $this->builder->string('province', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function city(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '城市';

        return $this->builder->string('city', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function district(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '县区';

        return $this->builder->string('district', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function street(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '街道';

        return $this->builder->string('street', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function area(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '地区';

        return $this->builder->string('area', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function address(string $comment = null, int $length = 60): Column
    {
        $comment = $comment ?: '地址';

        return $this->builder->string('address', $comment, $length)->default('');
    }

    /**
     * 经纬度，格式为 lng,lat（string 类型，只用于展示与透传，不做库内空间计算）
     * 如需范围查询、距离计算等空间运算，不要改用本方法，直接用
     * $builder->point('location', '经纬度') 重新构建 POINT 列，写入用 Point(lng, lat)，读取用 ST_X() / ST_Y()
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function location(string $comment = null, int $length = 60): Column
    {
        $comment = $comment ?: '经纬度';

        return $this->builder->string('location', $comment, $length)->default('');
    }

    /**
     * 经度（decimal(10,6)，可分列做范围查询）
     * @param string|null $comment
     * @return Column
     */
    public function lng(string $comment = null): Column
    {
        $comment = $comment ?: '经度';

        return $this->builder->decimal('lng', $comment, 10, 6);
    }

    /**
     * 纬度（decimal(10,6)，可分列做范围查询）
     * @param string|null $comment
     * @return Column
     */
    public function lat(string $comment = null): Column
    {
        $comment = $comment ?: '纬度';

        return $this->builder->decimal('lat', $comment, 10, 6);
    }
}