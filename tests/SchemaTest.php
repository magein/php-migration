<?php

namespace Tests;

use magein\migration\Schema;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    public function test_string_options_become_comment(): void
    {
        $schema = new Schema('用户表');

        $options = $schema->toArray();
        $this->assertSame('用户表', $options['comment']);
        $this->assertFalse($options['id']);
        $this->assertSame('InnoDB', $options['engine']);
        $this->assertSame('utf8_unicode_ci', $options['collation']);
    }

    public function test_array_options_keep_other_defaults(): void
    {
        $schema = new Schema(['comment' => '注释', 'engine' => 'MyISAM']);

        $options = $schema->toArray();
        $this->assertSame('注释', $options['comment']);
        $this->assertSame('MyISAM', $options['engine']);
        $this->assertSame('utf8_unicode_ci', $options['collation']);
        $this->assertFalse($options['id']);
    }

    public function test_unknown_option_is_rejected(): void
    {
        $schema = new Schema(['comment' => '表', 'not_in_whitelist' => 1]);

        $this->assertArrayNotHasKey('not_in_whitelist', $schema->toArray());
    }

    public function test_from_returns_same_instance(): void
    {
        $schema = new Schema('用户表');

        $this->assertSame($schema, Schema::from($schema));
    }

    public function test_from_wraps_string(): void
    {
        $schema = Schema::from('用户表');

        $this->assertInstanceOf(Schema::class, $schema);
        $this->assertSame('用户表', $schema->toArray()['comment']);
    }

    public function test_setters(): void
    {
        $schema = new Schema();

        $schema->setComment('注释');
        $schema->setEngine('MyISAM');
        $schema->setCollation('utf8mb4_unicode_ci');
        $schema->id();
        $options = $schema->toArray();
        $this->assertSame('注释', $options['comment']);
        $this->assertSame('MyISAM', $options['engine']);
        $this->assertSame('utf8mb4_unicode_ci', $options['collation']);
        $this->assertTrue($options['id']);

        $schema->setEngineInnodb();
        $schema->setEngineMyISAM();
        $this->assertSame('MyISAM', $schema->toArray()['engine']);
    }

    public function test_set_primary_key(): void
    {
        $schema = new Schema();

        $schema->setPrimaryKey(['user_id', 'tenant_id']);
        $options = $schema->toArray();
        $this->assertFalse($options['id']);
        $this->assertSame(['user_id', 'tenant_id'], $options['primary_key']);

        $schema->setPrimaryKey('user_id');
        $options = $schema->toArray();
        $this->assertFalse($options['id']);
        $this->assertSame('user_id', $options['primary_key']);
    }

    public function test_set_primary_key_disables_auto_id(): void
    {
        $schema = new Schema();
        $schema->id();
        $schema->setPrimaryKey('user_id');

        $this->assertFalse($schema->toArray()['id']);
    }

    public function test_set_primary_key_requires_explicit_columns(): void
    {
        foreach ([null, '', [], 0, ['user_id', '']] as $value) {
            try {
                (new Schema())->setPrimaryKey($value);
                $this->fail('空主键应当抛异常');
            } catch (\InvalidArgumentException $e) {
                $this->assertStringContainsString('主键', $e->getMessage());
            }
        }
    }
}
