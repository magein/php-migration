<?php

use magein\migration\Migrator;

class RenameUserToUsers extends Migrator
{
    protected $name = 'user';

    protected $new_name = 'users';

    public function up()
    {
        $this->table($this->name)->rename($this->new_name)->save();
    }

    public function down()
    {
        $this->table($this->new_name)->rename($this->name)->save();
    }
}