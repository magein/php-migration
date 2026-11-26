<?php

namespace tests;

use magein\migration\PhinxInput;
use Phinx\Component\Input\ArgvInput;
use PHPUnit\Framework\TestCase;

class PhinxInputTest extends TestCase
{
    /**
     * @param array $argv
     * @return ArgvInput
     */
    private function input(array $argv): ArgvInput
    {
        return (new PhinxInput())->init($argv);
    }

    public function test_registered_command_is_passed_through_verbatim(): void
    {
        $rendered = (string)$this->input([
            'migrate.php', 'migrate', '-e', 'production', '--target', '20250802000012',
        ]);

        $this->assertStringContainsString('migrate -e production', $rendered);
        $this->assertStringContainsString('--target 20250802000012', $rendered);
    }

    public function test_create_is_kept_as_native_command(): void
    {
        $rendered = (string)$this->input(['migrate.php', 'create', 'CreateUser']);

        $this->assertStringContainsString('create CreateUser', $rendered);
        $this->assertStringNotContainsString('--class=', $rendered);
    }

    public function test_seed_commands_are_passed_through(): void
    {
        $rendered = (string)$this->input(['migrate.php', 'seed:run', '-e', 'production']);

        $this->assertStringContainsString('seed:run', $rendered);
        $this->assertStringContainsString('-e production', $rendered);
    }

    public function test_global_option_is_passed_through(): void
    {
        $this->assertStringContainsString('--help', (string)$this->input(['migrate.php', '--help']));
    }

    public function test_bare_name_becomes_create_with_class_injection(): void
    {
        $rendered = (string)$this->input(['migrate.php', 'create_user']);

        $this->assertStringContainsString('create CreateUser', $rendered);
        $this->assertStringContainsString('--class=', $rendered);
        $this->assertStringContainsString('PhinxCreation', $rendered);
    }

    public function test_create_prefix_is_not_added(): void
    {
        $rendered = (string)$this->input(['migrate.php', 'user_order']);

        $this->assertStringContainsString('UserOrder', $rendered);
        $this->assertStringNotContainsString('CreateUserOrder', $rendered);
    }

    public function test_invalid_class_name_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('迁移类名不合法');

        $this->input(['migrate.php', 'user--name']);
    }

    public function test_run_anywhere_becomes_migrate(): void
    {
        foreach ([['run', 'create_user'], ['create_user', 'run']] as $tail) {
            $input = $this->input(array_merge(['migrate.php'], $tail));

            $this->assertSame('migrate', $input->getFirstArgument());
            $this->assertStringNotContainsString('create_user', (string)$input);
        }
    }

    public function test_run_keeps_only_environment(): void
    {
        $rendered = (string)$this->input([
            'migrate.php', 'run', '-e', 'production', '--target', '20250802000012',
        ]);

        $this->assertStringContainsString('-e production', $rendered);
        $this->assertStringNotContainsString('--target', $rendered);
    }

    public function test_convenient_create_keeps_only_environment(): void
    {
        $rendered = (string)$this->input([
            'migrate.php', 'create_user', '-e', 'production', '-t', '2025',
        ]);

        $this->assertStringContainsString('create CreateUser', $rendered);
        $this->assertStringContainsString('-e production', $rendered);
        $this->assertStringNotContainsString('-t 2025', $rendered);
    }

    public function test_long_environment_option_is_kept(): void
    {
        $rendered = (string)$this->input(['migrate.php', 'run', '--environment=production']);

        $this->assertStringContainsString('--environment=production', $rendered);
    }

    /**
     * -e 悬空（末尾无值）会被静默丢弃打到 default 环境，必须直接拒绝
     */
    public function test_dangling_environment_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('环境名缺失');

        $this->input(['migrate.php', 'run', '-e']);
    }

    /**
     * -e 后面跟的是另一个选项时视为漏了环境名，同样拒绝
     */
    public function test_environment_value_is_not_another_option_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('环境名缺失');

        $this->input(['migrate.php', 'run', '-e', '--fake']);
    }

    public function test_command_with_run_as_value_is_not_rewritten(): void
    {
        $rendered = (string)$this->input(['migrate.php', 'migrate', '-e', 'run']);

        $this->assertStringContainsString('-e run', $rendered);
    }

    public function test_set_class_and_configuration_are_injected(): void
    {
        $rendered = (string)(new PhinxInput())
            ->setClass('MyCreator')
            ->setConfiguration('/path/phinx.php')
            ->init(['migrate.php', 'create_user']);

        $this->assertStringContainsString('--class=MyCreator', $rendered);
        $this->assertStringContainsString('phinx.php', $rendered);
    }
}
