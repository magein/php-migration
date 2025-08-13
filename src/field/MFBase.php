<?php

namespace magein\migration\field;

use magein\migration\Builder;

class MFBase
{
    public Builder $builder;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }
}