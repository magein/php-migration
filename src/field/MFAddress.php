<?php

namespace magein\migration\field;

use magein\migration\Builder;
use magein\migration\Column;

class MFAddress
{
    protected Builder $builder;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function province(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '省份';

        return $this->builder->string('province_code', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function city(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '城市';

        return $this->builder->string('city_code', $comment, $length)->default('');
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

        return $this->builder->string('district', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function area(string $comment = null, int $length = 30): Column
    {
        $comment = $comment ?: '地区';

        return $this->builder->string('area_code', $comment, $length)->default('');
    }

    /**
     * @param string|null $comment
     * @param int $length
     * @return Column
     */
    public function address(string $comment = null, int $length = 60): Column
    {
        $comment = $comment ?: '地址';

        return $this->builder->string('status', $comment, $length)->default('');
    }
}