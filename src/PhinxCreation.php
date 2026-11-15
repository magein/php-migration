<?php

namespace magein\migration;

use Phinx\Migration\CreationInterface;
use Phinx\Component\Input\InputInterface;
use Phinx\Component\Output\OutputInterface;

class PhinxCreation implements CreationInterface
{
    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    protected $template = 'migrate';

    protected $table_name = '';

    public function __construct(?InputInterface $input = null, ?OutputInterface $output = null)
    {

        if ($input !== null) {
            $this->setInput($input);
        }
        if ($output !== null) {
            $this->setOutput($output);
        }

        $this->tableName();

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
        if (str_starts_with($name, 'Create')) {
            $this->template = 'create';
            $table_name = preg_replace(['/^Create/', '/Table$/'], ['', ''], $name);
            $table_name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $table_name));
        } else {
            $this->template = 'update';
            $table_name = preg_replace('/Update.*/', '', $name);
        }

        if ($table_name) {
            $table_name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $table_name));
        }

        return $this->table_name = $table_name ?? '';
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
