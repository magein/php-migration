<?php

namespace magein\migration;

use Phinx\Component\Input\ArgvInput;
use Phinx\Component\Input\InputDefinition;
use Phinx\Console\PhinxApplication;
use Phinx\Util\Util;

/**
 * 命令行输入归一化：只看第一个参数，不再猜测选项与类名的位置。
 *
 * 1. 已注册命令（migrate / rollback / create / seed:run ...）与 - 开头的全局选项：原样透传。
 *    要带 -c / -p / -t / -d 等参数就用原生命令。
 * 2. 任意位置出现裸词 run：等价 migrate，只保留 -e / --environment，其余参数丢弃。
 * 3. 其它裸词：便捷创建 —— 补 create、类名转驼峰并校验、注入 --class，只保留 -e。
 *
 * 便捷创建不补 create_ 前缀：create_ 开头走建表骨架，其余走变更骨架，建议统一用 create_ 开头。
 *
 * @see \magein\migration\PhinxCreation
 */
class PhinxInput
{
    /**
     * 自定义迁移创建类
     * @var string
     */
    public string $class = '';

    /**
     * 自定义配置文件路径
     * @var string
     */
    public string $configuration = '';

    /**
     * @param string $class
     * @return $this
     */
    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @param string $configuration
     * @return $this
     */
    public function setConfiguration(string $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * 下划线转驼峰
     * @param string $name
     * @return string
     */
    protected function camelize(string $name): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
    }

    /**
     * 已注册命令，从 PhinxApplication 读取以免与上游漂移
     * @return array
     */
    protected function commands(): array
    {
        return array_keys((new PhinxApplication())->all());
    }

    /**
     * 参数中是否已声明某个选项
     * @param array $args
     * @param array $names
     * @return bool
     */
    protected function hasOption(array $args, array $names): bool
    {
        foreach ($args as $token) {
            foreach ($names as $name) {
                if ($token === $name || strpos($token, $name . '=') === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * 只抽出 -e / --environment（及其值），run 与便捷创建共用
     * -e / --environment 必须带环境名：末尾悬空或值是下一个选项（- 开头）时抛异常，
     * 避免静默丢弃后打到 default 环境
     * @param array $args
     * @return array
     */
    protected function environment(array $args): array
    {
        $result = [];
        $count = count($args);

        for ($i = 0; $i < $count; $i++) {
            $token = $args[$i];

            if (strpos($token, '--environment=') === 0) {
                $result[] = $token;
            } elseif ($token === '-e' || $token === '--environment') {
                if (!isset($args[$i + 1]) || strpos($args[$i + 1], '-') === 0) {
                    throw new \InvalidArgumentException('环境名缺失：-e / --environment 后面必须跟环境名');
                }

                $result[] = $token;
                $result[] = $args[++$i];
            }
        }

        return $result;
    }

    /**
     * 归一为 Phinx 可识别的输入
     * @param array|null $argv
     * @param InputDefinition|null $definition
     * @return ArgvInput
     */
    public function init(?array $argv = null, ?InputDefinition $definition = null)
    {
        $argv = $argv ?? $_SERVER['argv'] ?? [];
        $script = array_shift($argv) ?? '';
        $args = array_values(array_map('strval', $argv));

        $head = $args[0] ?? '';

        // 全局选项（--help / --version）原样透传，不做任何注入
        if ($head === '' || strpos($head, '-') === 0) {
            return new ArgvInput(array_merge([$script], $args), $definition);
        }

        // 已注册命令原样透传；只有便捷写法才需要归一
        if (!in_array($head, $this->commands(), true)) {
            $environment = $this->environment($args);

            if (in_array('run', $args, true)) {
                $args = array_merge(['migrate'], $environment);
            } else {
                $class_name = $this->camelize($head);

                if (!Util::isValidPhinxClassName($class_name)) {
                    throw new \InvalidArgumentException(
                        '迁移类名不合法：' . $class_name . '（请使用 CamelCase）'
                    );
                }

                $args = array_merge(['create', $class_name], $environment);
                $args[] = '--class=' . ($this->class ?: '\magein\migration\PhinxCreation');
            }
        }

        if ($this->configuration && !$this->hasOption($args, ['--configuration', '-c'])) {
            $args[] = '--configuration=' . $this->configuration;
        }

        return new ArgvInput(array_merge([$script], $args), $definition);
    }
}
