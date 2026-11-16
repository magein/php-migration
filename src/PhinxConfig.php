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
     * 默认配置与传入配置递归合并：只传部分键不会丢掉其余默认值；
     * 列表值（如 paths.migrations 的多个路径）视为整体替换，不做追加
     * @param array $config
     * @return array
     */
    public static function config(array $config = []): array
    {
        return self::merge([
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

    /**
     * @param array $defaults 默认配置
     * @param array $overrides 覆盖配置
     * @return array
     */
    protected static function merge(array $defaults, array $overrides): array
    {
        foreach ($overrides as $key => $value) {
            if (isset($defaults[$key])
                && is_array($value)
                && is_array($defaults[$key])
                && !self::isList($value)
            ) {
                $defaults[$key] = self::merge($defaults[$key], $value);
            } else {
                $defaults[$key] = $value;
            }
        }

        return $defaults;
    }

    /**
     * 是否为连续数字键的列表
     * @param array $value
     * @return bool
     */
    private static function isList(array $value): bool
    {
        return $value === [] || array_keys($value) === range(0, count($value) - 1);
    }
}