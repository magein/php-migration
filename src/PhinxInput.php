<?php

namespace magein\migration;


use Phinx\Component\Input\ArgvInput;
use Phinx\Component\Input\InputArgument;
use Phinx\Component\Input\InputDefinition;
use Phinx\Component\Input\InputOption;

class PhinxInput
{

    public $class = '';

    public $configuratio = '';

    public function init(?array $argv = null, ?InputDefinition $definition = null)
    {
        $argv = $argv ?? $_SERVER['argv'] ?? [];

        $index = array_search('--column', $argv);
        $columns = '';
        if ($index !== false) {
            $columns = $argv[$index + 1] ?? '';
            unset($argv[$index], $argv[$index + 1]);
        }

        if (in_array('run', $argv)) {
            $argv[1] = 'migrate';
            $argv = array_slice($argv, 0, 2);
        } elseif (in_array('migrate', $argv)) {
            $argv[1] = 'migrate';
            $argv = array_slice($argv, 0, 2);
        } elseif (in_array('rollback', $argv)) {
            $argv[1] = 'rollback';
            $argv = array_slice($argv, 0, 2);
        } elseif (count($argv) === 2) {
            $name = $argv[1];
            if (!str_starts_with($name, 'seed')) {
                $argv[1] = 'create';
                $argv[2] = $name;
            }
        }

        $action = $argv[1] ?? '';
        if ($action === 'create') {
            $name = $argv[2] ?? '';
            if (empty($name)) {
                throw new \InvalidArgumentException('请输入表名称');
            }

            $name = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
            if (!preg_match('/[aA]ppend/', $name)
                && !preg_match('/[uU]pdate/', $name)
                && !preg_match('/[rR]ename/', $name)
                && !preg_match('/[rR]emove/', $name)
            ) {
                $name = sprintf('Create%sTable', $name);
            }

            $argv[2] = $name;

            if ($this->class) {
                $argv[] = '--class=' . $this->class;
            } else {
                $argv[] = '--class=\magein\migration\PhinxCreation';
            }
        }

        // 指定配置文件
        if ($this->configuratio) {
            $argv[] = '--configuration=' . $this->configuratio;
        }

        if ($index !== false) {
            $argv[] = '--column=' . $columns;
        }

        return new ArgvInput($argv, $definition);
    }
}