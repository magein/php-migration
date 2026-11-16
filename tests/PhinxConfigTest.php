<?php

namespace Tests;

use magein\migration\PhinxConfig;
use PHPUnit\Framework\TestCase;

class PhinxConfigTest extends TestCase
{
    public function test_partial_paths_merge_keeps_other_defaults(): void
    {
        $config = PhinxConfig::config(['paths' => ['migrations' => 'custom/migrations']]);

        $this->assertSame('custom/migrations', $config['paths']['migrations']);
        $this->assertSame('%%PHINX_CONFIG_DIR%%/database/seeds', $config['paths']['seeds']);
        $this->assertSame('creation', $config['version_order']);
        $this->assertArrayHasKey('file', $config['templates']);
    }

    public function test_list_value_is_replaced_not_merged(): void
    {
        $config = PhinxConfig::config(['paths' => ['migrations' => ['a', 'b']]]);

        $this->assertSame(['a', 'b'], $config['paths']['migrations']);
        $this->assertSame('%%PHINX_CONFIG_DIR%%/database/seeds', $config['paths']['seeds']);
    }

    public function test_scalar_replaced_and_environments_taken_as_whole(): void
    {
        $config = PhinxConfig::config([
            'version_order' => 'timestamp',
            'environments' => ['development' => ['adapter' => 'mysql', 'host' => 'localhost']],
        ]);

        $this->assertSame('timestamp', $config['version_order']);
        $this->assertSame('mysql', $config['environments']['development']['adapter']);
        $this->assertSame('localhost', $config['environments']['development']['host']);
    }

    public function test_no_arguments_returns_defaults(): void
    {
        $config = PhinxConfig::config();

        $this->assertSame('%%PHINX_CONFIG_DIR%%/database/migrations', $config['paths']['migrations']);
        $this->assertSame('%%PHINX_CONFIG_DIR%%/database/seeds', $config['paths']['seeds']);
        $this->assertSame('creation', $config['version_order']);
    }
}
