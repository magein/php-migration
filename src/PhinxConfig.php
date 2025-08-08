<?php

namespace magein\migration;

class PhinxConfig
{

    /**
     * @return string
     */
    public static function template(): string
    {
        return __DIR__ . '/stubs/migrate.stub';
    }

    /**
     * @param array $config
     * @return array
     */
    public static function config(array $config = []): array
    {
        return array_merge([
            'paths' => [
                'migrations' => '%%PHINX_CONFIG_DIR%%/database/migrations',
                'seeds' => '%%PHINX_CONFIG_DIR%%/database/seeds'
            ],
            'templates' => [
                'file' => self::template()
            ],
            'version_order' => 'creation',
            // 可选的外部依赖配置
            'external_dependencies' => [

            ]
        ], $config);
    }
}