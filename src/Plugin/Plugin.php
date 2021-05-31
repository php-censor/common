<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Plugin;

use PHPCensor\Common\Application\ApplicationInterface;
use PHPCensor\Common\Build\BuildErrorWriterInterface;
use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\Common\Build\BuildLoggerInterface;
use PHPCensor\Common\Build\BuildMetaWriterInterface;
use PHPCensor\Common\CommandExecutorInterface;
use PHPCensor\Common\PathResolverInterface;
use PHPCensor\Common\ParameterBag;
use PHPCensor\Common\Project\ProjectInterface;
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
    protected BuildInterface $build;

    /**
     * @var ProjectInterface
     */
    protected ProjectInterface $project;

    /**
     * @var ApplicationInterface
     */
    protected ApplicationInterface $application;

    /**
     * @var BuildLoggerInterface
     */
    protected BuildLoggerInterface $buildLogger;

    /**
     * @var BuildErrorWriterInterface
     */
    protected BuildErrorWriterInterface $buildErrorWriter;

    /**
     * @var BuildMetaWriterInterface
     */
    protected BuildMetaWriterInterface $buildMetaWriter;

    /**
     * @var CommandExecutorInterface
     */
    protected CommandExecutorInterface $commandExecutor;

    /**
     * @var VariableInterpolatorInterface
     */
    protected VariableInterpolatorInterface $variableInterpolator;

    /**
     * @var PathResolverInterface
     */
    protected PathResolverInterface $pathResolver;

    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @var ParameterBag
     */
    protected ParameterBag $options;

    /**
     * @var ParameterBag
     */
    protected ParameterBag $buildSettings;

    /**
     * Working directory for plugin (Directory with files for inspecting or directory with tests).
     * For example: `composer.phar --working-dir="<directory>"` install
     *
     * @var string
     */
    protected string $directory;

    /**
     * Array of ignoring files and directories. For example: ['.gitkeep', 'tests'].
     *
     * @var string[]
     */
    protected array $ignores;

    /**
     * Path for searching plugin binary (executable). For example: '/home/user/bin/'.
     *
     * @var string
     */
    protected string $binaryPath;

    /**
     * Names of the binary (executable) for searching in the binary path or in the build directory.
     *
     * @var array
     */
    protected array $binaryNames = [];

    /**
     * @var string
     */
    protected string $artifactsPluginPath;

    /**
     * @var string
     */
    protected string $artifactsPluginBranchPath;

    /**
     * @var string
     */
    protected string $artifactsPluginLink;

    /**
     * @var string
     */
    protected string $artifactsPluginBranchLink;

    /**
     * @param BuildInterface                $build
     * @param ProjectInterface              $project
     * @param BuildLoggerInterface          $buildLogger
     * @param BuildErrorWriterInterface     $buildErrorWriter
     * @param BuildMetaWriterInterface      $buildMetaWriter
     * @param CommandExecutorInterface      $commandExecutor
     * @param VariableInterpolatorInterface $variableInterpolator
     * @param PathResolverInterface         $pathResolver
     * @param ApplicationInterface          $application
     * @param ContainerInterface            $container
     *
     * @throws \Throwable
     */
    public function __construct(
        BuildInterface $build,
        ProjectInterface $project,
        BuildLoggerInterface $buildLogger,
        BuildErrorWriterInterface $buildErrorWriter,
        BuildMetaWriterInterface $buildMetaWriter,
        CommandExecutorInterface $commandExecutor,
        VariableInterpolatorInterface $variableInterpolator,
        PathResolverInterface $pathResolver,
        ApplicationInterface $application,
        ContainerInterface $container
    ) {
        $this->build                = $build;
        $this->project              = $project;
        $this->buildLogger          = $buildLogger;
        $this->buildErrorWriter     = $buildErrorWriter;
        $this->buildMetaWriter      = $buildMetaWriter;
        $this->commandExecutor      = $commandExecutor;
        $this->variableInterpolator = $variableInterpolator;
        $this->pathResolver         = $pathResolver;
        $this->application          = $application;
        $this->container            = $container;

        $this->initOptions();
        $this->initBuildSettings();

        $this->directory = $this->pathResolver->resolveDirectory(
            (string)$this->options->get('directory', '')
        );

        $this->ignores = $this->pathResolver->resolveIgnores(
            (array)$this->options->get('ignore', [])
        );

        $this->binaryPath = $this->pathResolver->resolveBinaryPath(
            (string)$this->options->get('binary_path', '')
        );

        /**
         * Example: /var/www/php-censor.localhost/public/artifacts/phpunit/2/10_xxxxxxxx/
         * Where: Project Id: 2, Build Id: 10
         */
        $this->artifactsPluginPath = \sprintf(
            '%s%s/%s/',
            $this->application->getArtifactsPath(),
            static::getName(),
            $this->build->getBuildDirectory()
        );

        /**
         * Example: /var/www/php-censor.localhost/public/artifacts/phpunit/2/master_xxxxxxxx/
         * Where: Project Id: 2, Branch: "master"
         */
        $this->artifactsPluginBranchPath = \sprintf(
            '%s%s/%s/',
            $this->application->getArtifactsPath(),
            static::getName(),
            $this->build->getBuildBranchDirectory()
        );

        /**
         * Example: https://php-censor.localhost/artifacts/phpunit/2/10_xxxxxxxx
         * Where: Project Id: 2, Build Id: 10
         */
        $this->artifactsPluginLink = \sprintf(
            '%s%s/%s',
            $this->application->getArtifactsLink(),
            static::getName(),
            $this->build->getBuildDirectory()
        );

        /**
         * Example: https://php-censor.localhost/artifacts/phpunit/2/master_xxxxxxxx
         * Where: Project Id: 2, Branch: "master"
         */
        $this->artifactsPluginBranchLink = \sprintf(
            '%s%s/%s',
            $this->application->getArtifactsLink(),
            static::getName(),
            $this->build->getBuildBranchDirectory()
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
     * @throws \Exception
     */
    protected function initOptions(): void
    {
        $projectConfig = $this->project->getBuildConfig();

        $pluginName         = $this->getName();
        $pluginOptionsArray = [];
        if (!empty($projectConfig[$pluginName])) {
            $pluginOptionsArray = $projectConfig[$pluginName];
        }

        $this->options = new ParameterBag($pluginOptionsArray);

        $this->buildLogger->logDebug('Plugin options: ' . \json_encode($this->options->all()));
    }

    protected function initBuildSettings(): void
    {
        $projectConfig = $this->project->getBuildConfig();

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
                $binaryNames = [$binaryNames];
            }

            $this->binaryNames = \array_unique(
                \array_merge($binaryNames, $this->binaryNames)
            );
        }

        $normalizedNames = [];
        foreach ($this->binaryNames as $binaryName) {
            if ($binaryName) {
                $normalizedNames[] = $binaryName;
            }
        }

        $this->binaryNames = $normalizedNames;
    }

    /**
     * @throws \Throwable
     */
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

    /**
     * Example: /var/www/php-censor.localhost/public/artifacts/phpunit/2/10_xxxxxxxx/report.xml
     * Where: Project Id: 2, Build Id: 10, File: report.xml
     *
     * @param string $file
     *
     * @return string
     */
    protected function getArtifactPath(string $file = ''): string
    {
        return \rtrim(\sprintf('%s%s', $this->artifactsPluginPath, $file), '/');
    }

    /**
     * Example: /var/www/php-censor.localhost/public/artifacts/phpunit/2/master_xxxxxxxx/report.xml
     * Where: Project Id: 2, Branch: "master", File: report.xml
     *
     * @param string $file
     *
     * @return string
     */
    protected function getArtifactPathForBranch(string $file = ''): string
    {
        return \rtrim(\sprintf('%s%s', $this->artifactsPluginBranchPath, $file), '/');
    }

    /**
     * Example: https://php-censor.localhost/artifacts/phpunit/2/10_xxxxxxxx/report.xml
     * Where: Project Id: 2, Build Id: 10, File: report.xml
     *
     * @param string $file
     *
     * @return string
     */
    protected function getArtifactLink(string $file = ''): string
    {
        if ($file) {
            return \sprintf('%s/%s', $this->artifactsPluginLink, $file);
        }

        return $this->artifactsPluginLink;
    }

    /**
     * Example: https://php-censor.localhost/artifacts/phpunit/2/master_xxxxxxxx/report.xml
     * Where: Project Id: 2, Branch: "master", File: report.xml
     *
     * @param string $file
     *
     * @return string
     */
    protected function getArtifactLinkForBranch(string $file = ''): string
    {
        if ($file) {
            return \sprintf('%s/%s', $this->artifactsPluginBranchLink, $file);
        }

        return $this->artifactsPluginBranchLink;
    }
}
