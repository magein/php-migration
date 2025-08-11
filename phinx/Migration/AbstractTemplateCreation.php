<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Phinx\Migration;

use Phinx\Component\Input\InputInterface;
use Phinx\Component\Output\OutputInterface;

abstract class AbstractTemplateCreation implements CreationInterface
{
    /**
     * @var \Phinx\Component\Input\InputInterface
     */
    protected $input;

    /**
     * @var \Phinx\Component\Output\OutputInterface
     */
    protected $output;

    /**
     * @param \Phinx\Component\Input\InputInterface|null $input Input
     * @param \Phinx\Component\Output\OutputInterface|null $output Output
     */
    public function __construct(?InputInterface $input = null, ?OutputInterface $output = null)
    {
        if ($input !== null) {
            $this->setInput($input);
        }
        if ($output !== null) {
            $this->setOutput($output);
        }
    }

    /**
     * @inheritDoc
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @inheritDoc
     */
    public function setInput(InputInterface $input): CreationInterface
    {
        $this->input = $input;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * @inheritDoc
     */
    public function setOutput(OutputInterface $output): CreationInterface
    {
        $this->output = $output;

        return $this;
    }
}
