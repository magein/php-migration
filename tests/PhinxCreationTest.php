<?php

namespace tests;

use magein\migration\PhinxCreation;
use Phinx\Component\Input\InputInterface;
use PHPUnit\Framework\TestCase;

class PhinxCreationTest extends TestCase
{
    /**
     * @param string $name
     * @return array [template, table_name]
     */
    private function creation(string $name): array
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('name')->willReturn($name);

        $creation = new PhinxCreation($input);
        // 表名推断已延后到 getMigrationTemplate()，反射前先触发一次
        $creation->getMigrationTemplate();

        $template = new \ReflectionProperty(PhinxCreation::class, 'template');
        $template->setAccessible(true);
        $table_name = new \ReflectionProperty(PhinxCreation::class, 'table_name');
        $table_name->setAccessible(true);

        return [$template->getValue($creation), $table_name->getValue($creation)];
    }

    public function nameProvider(): array
    {
        // PhinxCreation 只识别 Create 驼峰前缀；下划线小写名在 CLI 链路里已由
        // PhinxInput 先驼峰化（create_user → CreateUser），单独传入按变更类处理
        return [
            'CreateUser' => ['CreateUser', 'create', 'user'],
            'CreateUserTable' => ['CreateUserTable', 'create', 'user'],
            'create_user (not camelized)' => ['create_user', 'update', ''],
            'update_user' => ['update_user', 'update', ''],
            'user_append_balance' => ['user_append_balance', 'update', ''],
        ];
    }

    /**
     * @dataProvider nameProvider
     * @param string $name
     * @param string $expected_template
     * @param string $expected_table
     */
    public function test_table_name_inference(string $name, string $expected_template, string $expected_table): void
    {
        [$template, $table_name] = $this->creation($name);

        $this->assertSame($expected_template, $template);
        $this->assertSame($expected_table, $table_name);
    }

    public function test_create_template_contains_inferred_table_name(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('name')->willReturn('CreateUser');

        $creation = new PhinxCreation($input);

        $this->assertStringContainsString("protected string \$name = 'user';", $creation->getMigrationTemplate());
    }

    public function test_update_template_keeps_empty_table_name(): void
    {
        $input = $this->createMock(InputInterface::class);
        $input->method('getArgument')->with('name')->willReturn('update_user');

        $creation = new PhinxCreation($input);

        $this->assertStringContainsString("protected string \$name = '';", $creation->getMigrationTemplate());
    }
}
