<?php

namespace magein\migration\field;

use magein\migration\Builder;

class MFBase
{
    /**
     * @var Builder
     */
    public $builder;

    public function __construct($builder)
    {
        $this->builder = $builder;
    }
}