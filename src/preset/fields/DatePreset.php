<?php

namespace magein\migration\preset\fields;

use magein\migration\Column;
use magein\migration\preset\PresetGroup;

class DatePreset extends PresetGroup
{

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
}
