<?php

use magein\migration\Migrator;

class UserAppend extends Migrator
{
    protected $name = 'user';

    public function up()
    {
        $builder = $this->builder();

        $builder->user->points();
        $builder->user->score();
        $builder->user->idType();
        $builder->user->idNumber();

        $this->update($builder);
    }

    public function down()
    {
        if ($this->hasColumn('points')) {
            $this->removeColumn('points');
        }

        if ($this->hasColumn('score')) {
            $this->removeColumn('score');
        }

        if ($this->hasColumn('id_type')) {
            $this->removeColumn('id_type');
        }

        if ($this->hasColumn('id_number')) {
            $this->removeColumn('id_number');
        }
    }
}