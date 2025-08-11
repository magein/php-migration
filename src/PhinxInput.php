<?php

namespace magein\migration;


use Phinx\Component\Input\ArgvInput;
use Phinx\Component\Input\InputDefinition;

class PhinxInput extends ArgvInput
{

    /**
     * @param array|null $argv
     * @param InputDefinition|null $definition
     */
    public function __construct(?array $argv = null, ?InputDefinition $definition = null)
    {
        $argv = $argv ?? $_SERVER['argv'] ?? [];

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

            $argv[] = '--class=\magein\migration\PhinxCreation';

            // 指定配置参数
            //$argv[] = '--configuration=phinx.php';
        }

        parent::__construct($argv, $definition);
    }
}