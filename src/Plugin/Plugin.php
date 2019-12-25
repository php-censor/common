<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Plugin;

use PHPCensor\Common\Build\BuildErrorWriterInterface;
use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\Common\Build\BuildLoggerInterface;
use PHPCensor\Common\Build\BuildMetaWriterInterface;
use PHPCensor\Common\CommandExecutorInterface;
use PHPCensor\Common\PathResolverInterface;
use PHPCensor\Common\Plugin\Plugin\ParameterBag;
use PHPCensor\Common\VariableInterpolatorInterface;
use Psr\Container\ContainerInterface;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
abstract class Plugin implements PluginInterface
{
    /**
     * @var BuildInterface
     */
    protected $build;

    /**
     * @var BuildLoggerInterface
     */
    protected $buildLogger;

    /**
     * @var BuildErrorWriterInterface
     */
    protected $buildErrorWriter;

    /**
     * @var BuildMetaWriterInterface
     */
    protected $buildMetaWriter;

    /**
     * @var CommandExecutorInterface
     */
    protected $commandExecutor;

    /**
     * @var VariableInterpolatorInterface
     */
    protected $variableInterpolator;

    /**
     * @var PathResolverInterface
     */
    protected $pathResolver;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var ParameterBag
     */
    protected $options;

    /**
     * @var ParameterBag
     */
    protected $buildSettings;

    /**
     * Working directory for plugin (Directory with files for inspecting or directory with tests).
     * For example: `composer.phar --working-dir="directory"` install
     *
     * @var string
     */
    protected $directory;

    /**
     * Array of ignoring files and directories. For example: ['.gitkeep', 'tests'].
     *
     * @var string[]
     */
    protected $ignores;

    /**
     * Path for searching plugin binary (executable). For example '/home/user/bin/'.
     *
     * @var string
     */
    protected $binaryPath;

    /**
     * Names of the binary (executable) for searching in the binary path or in the build directory.
     *
     * @var array
     */
    protected $binaryNames = [];

    /**
     * @param BuildInterface                $build
     * @param BuildLoggerInterface          $buildLogger
     * @param BuildErrorWriterInterface     $buildErrorWriter
     * @param BuildMetaWriterInterface      $buildMetaWriter
     * @param CommandExecutorInterface      $commandExecutor
     * @param VariableInterpolatorInterface $variableInterpolator
     * @param PathResolverInterface         $pathResolver
     * @param ContainerInterface            $container
     * @param array                         $projectConfig
     *
     * @throws \Exception
     */
    public function __construct(
        BuildInterface $build,
        BuildLoggerInterface $buildLogger,
        BuildErrorWriterInterface $buildErrorWriter,
        BuildMetaWriterInterface $buildMetaWriter,
        CommandExecutorInterface $commandExecutor,
        VariableInterpolatorInterface $variableInterpolator,
        PathResolverInterface $pathResolver,
        ContainerInterface $container,
        array $projectConfig = []
    ) {
        $this->build                = $build;
        $this->buildLogger          = $buildLogger;
        $this->buildErrorWriter     = $buildErrorWriter;
        $this->buildMetaWriter      = $buildMetaWriter;
        $this->commandExecutor      = $commandExecutor;
        $this->variableInterpolator = $variableInterpolator;
        $this->pathResolver         = $pathResolver;
        $this->container            = $container;

        $this->initOptions($projectConfig);
        $this->initBuildSettings($projectConfig);

        $this->directory = $this->pathResolver->resolveDirectory(
            (string)$this->options->get('directory', '')
        );

        $this->ignores = $this->pathResolver->resolveIgnores(
            (array)$this->options->get('ignore', [])
        );

        $this->binaryPath = $this->pathResolver->resolveBinaryPath(
            (string)$this->options->get('binary_path', '')
        );

        $this->initBinaryNames();
        $this->initPluginSettings();
    }

    /**
     * {@inheritdoc}
     */
    abstract public static function getName(): string;

    /**
     * {@inheritdoc}
     */
    abstract public function execute(): bool;

    /**
     * {@inheritdoc}
     */
    public static function canExecute(string $stage, BuildInterface $build): bool
    {
        return false;
    }

    /**
     * @param array $projectConfig
     *
     * @throws \Exception
     */
    protected function initOptions(array $projectConfig): void
    {
        $pluginName         = $this->getName();
        $pluginOptionsArray = [];
        if (!empty($projectConfig[$pluginName])) {
            $pluginOptionsArray = $projectConfig[$pluginName];
        }

        $this->options = new ParameterBag($pluginOptionsArray);

        $this->buildLogger->logDebug('Plugin options: ' . \json_encode($this->options->all()));
    }

    /**
     * @param array $projectConfig
     */
    protected function initBuildSettings(array $projectConfig): void
    {
        $buildSettingArray = [];
        if (!empty($projectConfig['build_settings'])) {
            $buildSettingArray = $projectConfig['build_settings'];
        }

        $this->buildSettings = new ParameterBag($buildSettingArray);
    }

    protected function initBinaryNames(): void
    {
        $this->binaryNames = $this->getPluginDefaultBinaryNames();
        if ($this->options->has('binary_name')) {
            $binaryNames = $this->options->get('binary_name', []);
            if (!\is_array($binaryNames)) {
                $binaryNames = [(string)$binaryNames];
            }

            $this->binaryNames = \array_unique(
                \array_merge($binaryNames, $this->binaryNames)
            );
        }

        $normalizedNames = [];
        foreach ($this->binaryNames as $binaryName) {
            if ($binaryName) {
                $normalizedNames[] = (string)$binaryName;
            }
        }

        $this->binaryNames = $normalizedNames;
    }

    protected function initPluginSettings(): void
    {
    }

    /**
     * If plugin has binary (executable) method should return array of default binary names.
     * For example: ['executable', 'executable.phar'].
     *
     * @return array
     */
    protected function getPluginDefaultBinaryNames(): array
    {
        return [];
    }
}
