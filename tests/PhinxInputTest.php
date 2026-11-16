<?php

namespace Tests;

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

    public function test_run_is_alias_of_migrate(): void
    {
        $this->assertSame('migrate', $this->input(['migrate.php', 'run', '-e', 'production'])->getFirstArgument());
    }

    public function test_bare_class_name_becomes_create_with_class_injection(): void
    {
        $input = $this->input(['migrate.php', 'create_user']);
        $rendered = (string)$input;

        $this->assertSame('create', $input->getFirstArgument());
        $this->assertStringContainsString('CreateUser', $rendered);
        $this->assertStringContainsString('--class=', $rendered);
        $this->assertStringContainsString('PhinxCreation', $rendered);
    }

    public function test_explicit_create_normalizes_underscore_name(): void
    {
        $this->assertStringContainsString(
            'UserAppendBalance',
            (string)$this->input(['migrate.php', 'create', 'user_append_balance'])
        );
    }

    public function test_options_are_passed_through(): void
    {
        $rendered = (string)$this->input([
            'migrate.php', 'status', '--target', '20250802000012', '-e', 'production',
        ]);

        $this->assertStringContainsString('status', $rendered);
        $this->assertStringContainsString('--target 20250802000012', $rendered);
        $this->assertStringContainsString('-e production', $rendered);
    }

    public function test_existing_class_option_is_not_overridden(): void
    {
        $rendered = (string)$this->input(['migrate.php', 'create', '-l', 'MyCreator', 'create_user']);

        $this->assertStringContainsString('MyCreator', $rendered);
        $this->assertSame(0, substr_count($rendered, '--class='), '已用 -l 指定创建类时不应再注入 --class');
    }

    public function test_unknown_lowercase_word_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('无法识别的命令：wrongcmd');

        $this->input(['migrate.php', 'wrongcmd']);
    }

    public function test_joined_short_option_value_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('不支持的选项写法');

        // 校验发生在 create 的类名定位路径上；选项在首位时属于全局选项，原样透传
        $this->input(['migrate.php', 'create', '-eproduction', 'create_user']);
    }

    public function test_create_without_name_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('请输入表名称');

        $this->input(['migrate.php', 'create']);
    }

    public function test_global_options_are_passed_through_verbatim(): void
    {
        $this->assertStringContainsString('--help', (string)$this->input(['migrate.php', '--help']));
    }
}
