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

    public function setInput(InputInterface $input)
    {
        $this->input = $input;

        return $this;
    }

    public function getInput(): InputInterface
    {
        return $this->input;
    }

    public function setOutput(OutputInterface $output)
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
        $table_name = '';
        $name = $this->input->getArgument('name');
        if (preg_match('/^Create/', $name)) {
            $this->template = 'create';
            $table_name = preg_replace(['/^Create/', '/Table$/'], ['', ''], $name);
            $table_name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $table_name));
        }

        if (preg_match('/Append$/', $name)) {
            $this->template = 'append';
            $table_name = preg_replace('/Append$/', '', $name);
        }

        if (preg_match('/Update$/', $name)) {
            $this->template = 'update';
            $table_name = preg_replace('/Update$/', '', $name);
        }

        if (preg_match('/Rename$/', $name)) {
            $this->template = 'rename';
            $table_name = preg_replace('/Rename$/', '', $name);
        }

        if (preg_match('/Remove$/', $name)) {
            $this->template = 'remove';
            $table_name = preg_replace('/Remove$/', '', $name);
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
