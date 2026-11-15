<?php

namespace magein\migration;

use Phinx\Component\Input\ArgvInput;
use Phinx\Component\Input\InputDefinition;
use Phinx\Console\PhinxApplication;

/**
 * Phinx 命令行输入归一化
 *
 * argv 格式：<脚本名> <命令 | 迁移类名> [选项...]
 *
 * 命令：使用 PhinxApplication 已注册的命令名（migrate / rollback / status /
 *       breakpoint / create / init / test / seed:create / seed:run / list / help 等），
 *       run 是 migrate 的别名；命令之后的选项原样透传给 Phinx。
 * 类名：单个裸词且含大写或下划线时视为迁移类名，自动转成驼峰并补 create，
 *       例如 create_user → CreateUser、AppendUserProfile → AppendUserProfile。
 *       纯小写单词一律按命令解析，拼错时直接报错，不会误生成迁移文件。
 * 选项：只支持 -x（值单独跟在后面，如 -e production）和
 *       --name / --name=value（如 --target 2025... / --class=MyCreator）两种写法；
 *       不支持 -eproduction 这类短选项连写值，选项请放在命令之后。
 * 注入：create 时若未指定 --class，自动追加 PhinxCreation；
 *       设置了 configuration 且命令行未指定 --configuration 时自动追加。
 *
 * @see \magein\migration\PhinxCreation
 */
class PhinxInput
{
    /**
     * 自定义迁移创建类
     * @var string
     */
    public $class = '';

    /**
     * 自定义配置文件路径
     * @var string
     */
    public $configuration = '';

    /**
     * 创建迁移时可接收值的选项
     * 用于在参数中跳过选项的值，正确定位迁移类名
     * @var array
     */
    protected $value_options = [
        '-e', '-t', '-d', '-c', '-p', '-l', '-s', '-f',
        '--environment', '--target', '--date', '--configuration',
        '--parser', '--class', '--path', '--template', '--style',
        '--seed', '--format',
    ];

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
     * 把下划线命名转换为驼峰命名
     * @param string $name
     * @return string
     */
    protected function camelize(string $name): string
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
    }

    /**
     * 已注册的命令名，从 PhinxApplication 读取以免与上游漂移
     * @return array
     */
    protected function commands(): array
    {
        return array_keys((new PhinxApplication())->all());
    }

    /**
     * 选项的值是否为紧随其后的独立 token（-e production / --target 2025...）
     * @param string $token
     * @return bool
     */
    protected function consumesNextToken(string $token): bool
    {
        // --name=value 已把值写在同一 token 里
        if (strpos($token, '=') !== false) {
            return false;
        }

        return in_array($token, $this->value_options, true);
    }

    /**
     * 选项写法是否合法：只允许 -x 与 --name / --name=value
     * @param string $token
     * @return bool
     */
    protected function isOption(string $token): bool
    {
        return (bool) preg_match('/^-(?:[a-zA-Z]|-[a-zA-Z][a-zA-Z0-9-]*(?:=.*)?)$/', $token);
    }

    /**
     * 参数中是否已经声明某个选项
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
     * 定位 create 命令的迁移类名（第一个非选项 token）
     * @param array $args
     * @return int|null
     */
    protected function findNameIndex(array $args): ?int
    {
        $count = count($args);

        for ($i = 1; $i < $count; $i++) {
            $token = $args[$i];

            if ($token === '--') {
                return isset($args[$i + 1]) ? $i + 1 : null;
            }

            if ($token === '') {
                continue;
            }

            if ($token[0] === '-') {
                if (!$this->isOption($token)) {
                    throw new \InvalidArgumentException(
                        '不支持的选项写法：' . $token . '（只支持 -x 或 --name[=value]）'
                    );
                }

                // 带值选项且值单独成词时，跳过它的值
                if ($this->consumesNextToken($token)
                    && isset($args[$i + 1]) && $args[$i + 1] !== ''
                    && $args[$i + 1][0] !== '-') {
                    $i++;
                }

                continue;
            }

            return $i;
        }

        return null;
    }

    /**
     * 把命令行参数归一为 Phinx 可识别的输入，其余 token 原样透传
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

        // 全局选项 / --help / --version 交回 Phinx 自己处理，不能当成迁移类名
        if ($head === '' || strpos($head, '-') === 0) {
            return new ArgvInput(array_merge([$script], $args), $definition);
        }

        if ($head === 'run') {
            $args[0] = 'migrate';
        } elseif (!in_array($head, $this->commands(), true)) {
            // 纯小写单词更像命令（含拼错的命令），不转成 create，避免误生成迁移文件。
            // 这里直接报错而不是透传：上游 Application::doRun() 遇到未知命令会
            // 吞掉异常后使用未定义的 $command，最终只吐一条 Notice 和大段调用栈。
            if (preg_match('/^[a-z]+$/', $head) || !preg_match('/^[A-Za-z][A-Za-z0-9_]*$/', $head)) {
                throw new \InvalidArgumentException('无法识别的命令：' . $head);
            }

            array_unshift($args, 'create');
        }

        if ($args[0] === 'create') {
            $index = $this->findNameIndex($args);
            if ($index === null) {
                throw new \InvalidArgumentException('请输入表名称');
            }

            $args[$index] = $this->camelize($args[$index]);

            if (!$this->hasOption($args, ['--class', '-l'])) {
                $args[] = '--class=' . ($this->class ?: '\magein\migration\PhinxCreation');
            }
        }

        if ($this->configuration && !$this->hasOption($args, ['--configuration', '-c'])) {
            $args[] = '--configuration=' . $this->configuration;
        }

        return new ArgvInput(array_merge([$script], $args), $definition);
    }
}
