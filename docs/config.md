# 配置文件

`phinx.php` 位于项目根目录，由 Phinx 自动读取。文件里是本机数据库连接信息，**不纳入版本控制**（已在 `.gitignore` 中忽略）；克隆仓库后按下面模板自行创建。本包只保留 `.php` 格式解析，`.yml` 配置已删除。

## 模板

在项目根目录新建 `phinx.php`，按本机实际数据库修改 `host` / `name` / `user` / `pass`：

```php
<?php

return \magein\migration\PhinxConfig::config(
    [
        'environments' => [
            'default_migration_table' => 'phinx_log',
            'default_environment' => 'development',
            'production' => [
                'adapter' => 'mysql',
                'host' => 'localhost',
                'name' => 'production_db',
                'user' => 'root',
                'pass' => '',
                'port' => 3306,
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ],
            'development' => [
                'adapter' => 'mysql',
                'host' => '127.0.0.1',
                'name' => 'php-migration',
                'user' => 'root',
                'pass' => '',
                'port' => 3306,
                'charset' => 'utf8',
            ],
            'testing' => [
                'adapter' => 'sqlite',
                'name' => './tests/_data/database',
                'memory' => false
            ]
        ],
    ]
);
```

## 字段说明

`PhinxConfig::config()` 先合并一份默认配置，再用传入的 `environments` 覆盖：

| 键                                                                                | 说明                                            |
|----------------------------------------------------------------------------------|-----------------------------------------------|
| `paths.migrations`                                                               | 默认 `%%PHINX_CONFIG_DIR%%/database/migrations` |
| `paths.seeds`                                                                    | 默认 `%%PHINX_CONFIG_DIR%%/database/seeds`      |
| `templates.file`                                                                 | 默认指向包内 `src/stubs/migrate.stub`               |
| `version_order`                                                                  | 默认 `creation`                                 |
| `default_migration_table`                                                        | 记录迁移版本的日志表，默认 `phinx_log`                     |
| `default_environment`                                                            | 未指定 `-e` 时使用的环境                               |
| `production` / `development` / `testing`                                         | 环境名可自定义，用 `-e` 选择                             |
| `adapter` / `host` / `name` / `user` / `pass` / `port` / `charset` / `collation` | 数据库连接参数                                       |

`%%PHINX_CONFIG_DIR%%` 会解析成配置文件所在目录，模板里的相对路径始终以项目根目录为基准。`testing` 用 sqlite，指向的 `./tests/_data/database` 需自行准备，本包不含测试数据。

## 指定配置

默认读取根目录 `phinx.php`，也可以放到别处用 `--configuration` 指定：

```shell
php migrate status -e production
php migrate migrate --configuration=/path/to/phinx.php
```

命令行与迁移文件的基本用法见 [根目录 README](../README.md)，字段类型与表结构场景见 [docs/README](README.md)。
