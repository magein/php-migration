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
     * @return void
     */
    public function _normal()
    {
        $this->province();
        $this->city();
        $this->district();
        $this->address();
    }
}