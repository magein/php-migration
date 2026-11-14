<?php

namespace magein\migration\preset;

use magein\migration\Builder;

abstract class PresetGroup
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