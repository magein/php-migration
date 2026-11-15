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
        $table = $this->table($this->name);

        foreach (['points', 'score', 'id_type', 'id_number'] as $column) {
            if ($table->hasColumn($column)) {
                $table->removeColumn($column);
            }
        }

        $table->save();
    }
}