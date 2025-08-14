<?php

namespace magein\migration\field;

use magein\migration\Column;

class MFIs extends MFGen
{

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function isHot(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '是否热门 0 否 no 1 是 yes';

        return $this->builder->integerTiny('is_hot', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function isRecommend(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '是否推荐 0 否 no 1 是 yes';

        return $this->builder->integerTiny('is_recommend', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function isPublished(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '是否发布 0 否 no 1 是 yes';

        return $this->builder->integerTiny('is_published', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function isOriginal(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '是否原创 0 否 no 1 是 yes';

        return $this->builder->integerTiny('is_original', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function isUp(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '是否上架 0 否 no 1 是 yes';

        return $this->builder->integerTiny('is_up', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function isForbid(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '是否禁用 0 否 no 1 是 yes';

        return $this->builder->integerTiny('is_forbid', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function isShow(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '是否展示 0 否 no 1 是 yes';

        return $this->builder->integerTiny('is_show', $comment)->default($default);
    }

    /**
     * 是否收藏
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function isCollection(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '是否展示 0 否 no 1 是 yes';

        return $this->builder->integerTiny('is_collection', $comment)->default($default);
    }

    /**
     * @param string|null $comment
     * @param int $default
     * @return Column
     */
    public function isSend(string $comment = null, int $default = 0): Column
    {
        $comment = $comment ?: '是否发送 0 否 no 1 是 yes';

        return $this->builder->integerTiny('is_send', $comment)->default($default);
    }
}