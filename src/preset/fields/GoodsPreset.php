<?php

namespace magein\migration\preset\fields;

use magein\migration\Column;
use magein\migration\preset\PresetGroup;

class GoodsPreset extends PresetGroup
{

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
    public function sku(string $comment = null): Column
    {
        $comment = $comment ?: '货号';

        return $this->builder->string('sku', $comment, 60)->default('');
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function sales(string $comment = null): Column
    {
        $comment = $comment ?: '销量';

        return $this->builder->integer('sales', $comment)->default(0);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function marketPrice(string $comment = null): Column
    {
        $comment = $comment ?: '市场价 单位分';

        return $this->builder->integer('market_price', $comment)->default(0);
    }

    /**
     * @param string|null $comment
     * @return Column
     */
    public function costPrice(string $comment = null): Column
    {
        $comment = $comment ?: '成本价 单位分';

        return $this->builder->integer('cost_price', $comment)->default(0);
    }
}
