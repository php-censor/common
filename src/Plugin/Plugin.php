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
     * @var string
     */
    protected $directory;

    /**
     * @var string[]
     */
    protected $ignores;

    /**
     * @var string
     */
    protected $binaryPath;

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

        $this->binaryPath = $this->pathResolver->resolveBinaryPath(
            (string)$this->options->get('binary_path', '')
        );

        $this->ignores = $this->pathResolver->resolveIgnores(
            (array)$this->options->get('ignore', [])
        );

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
     * @param array $projectConfig
     *
     * @throws \Exception
     */
    protected function initOptions(array $projectConfig)
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
    protected function initBuildSettings(array $projectConfig)
    {
        $buildSettingArray = [];
        if (!empty($projectConfig['build_settings'])) {
            $buildSettingArray = $projectConfig['build_settings'];
        }

        $this->buildSettings = new ParameterBag($buildSettingArray);
    }

    protected function initPluginSettings()
    {
    }
}
