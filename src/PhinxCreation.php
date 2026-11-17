<?php

namespace magein\migration;

use Phinx\Migration\CreationInterface;
use Phinx\Component\Input\InputInterface;
use Phinx\Component\Output\OutputInterface;

/**
 * CLI 自动注入 --class 走这里生成迁移骨架。
 *
 * 用 --class=你的类（实现 CreationInterface、getMigrationTemplate() 返回模板）
 * 即可替代 --template 读固定 stub：能按类名选 create/update 骨架、推断表名，
 * 并保证生成的迁移继承 Migrator。
 */
class PhinxCreation implements CreationInterface
{
    /**
     * @var InputInterface
     */
    protected InputInterface $input;

    /**
     * @var OutputInterface
     */
    protected OutputInterface $output;

    protected string $template = 'migrate';

    protected string $table_name = '';

    public function __construct(?InputInterface $input = null, ?OutputInterface $output = null)
    {

        if ($input !== null) {
            $this->setInput($input);
        }
        if ($output !== null) {
            $this->setOutput($output);
        }

    }

    public function setInput(InputInterface $input): PhinxCreation
    {
        $this->input = $input;

        return $this;
    }

    public function getInput(): InputInterface
    {
        return $this->input;
    }

    public function setOutput(OutputInterface $output): PhinxCreation
    {
        $this->output = $output;

        return $this;
    }

    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @return string
     */
    protected function tableName(): string
    {
        $name = $this->input->getArgument('name');

        // 只有 CreateXxx 推断表名，其余变更类不猜，留空由使用者手写
        $this->template = 'update';
        $table_name = '';

        if (strpos($name, 'Create') === 0) {
            $this->template = 'create';
            $table_name = preg_replace(['/^Create/', '/Table$/'], ['', ''], $name);
            $table_name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $table_name));
        }

        return $this->table_name = $table_name;
    }

    public function getMigrationTemplate(): string
    {
        $name = $this->tableName();

        $content = file_get_contents(__DIR__ . '/stubs/' . $this->template . '.stub');

        return str_replace('{$name}', $name, $content);
    }

    public function postMigrationCreation(string $migrationFilename, string $className, string $baseClassName): void
    {

    }
}
