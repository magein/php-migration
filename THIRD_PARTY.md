# 第三方代码与依赖说明

## 随包分发（`phinx/` 目录）

| 目录 | 来源 | 许可证 | 说明 |
| --- | --- | --- | --- |
| `phinx/Db/` `phinx/Config/` `phinx/Migration/` `phinx/Seed/` `phinx/Util/` `phinx/Wrapper/` `phinx/Console/` | [Phinx](https://github.com/cakephp/phinx) 0.14.0 | MIT | 命名空间保持 `Phinx\` 不变，文件头保留原版权声明；本包开发期间禁止修改该目录 |
| `phinx/Component/` | [Symfony Console](https://github.com/symfony/console) | MIT | 文件头保留 `(c) Fabien Potencier` 声明；提取时做过删改（删除 json/xml/md 描述器与 complete 命令等），详见 `phinx/Component/readme.md` |

- 上述上游均为 MIT 授权，与本包 `LICENSE` 兼容；再分发请同时保留目录内的原始版权头。
- `phinx/Component/` 命名空间改为 `Phinx\Component\*`，与上游 Symfony 类一一对应。

## 运行时依赖

本包运行时**零 composer 依赖**（测试依赖 phpunit 除外，见 `composer.json` 的 `require-dev`）：

| 依赖 | 用途 |
| --- | --- |
| `php ^7.4` | 语言版本 |
| `ext-pdo` | PDO 连接与对应数据库驱动（如 `pdo_mysql`）由使用者按需启用 |

`phinx/` 源码里少量类型声明引用了 `Cake\Database\*` 与 `Psr\Container\*`（如 `getDecoratedConnection()`、`Config::getContainer()`），这些路径在本包的迁移链路中不会被调用（常规迁移走 PDO 直连，容器不配置时返回 `null` 也不触发类加载），因此无需安装对应 composer 包。若直接调用 `getQueryBuilder()` 等方法则需自行引入 `cakephp/database`。
