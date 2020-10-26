<?php

declare(strict_types = 1);

namespace Tests\PHPCensor\Common\Plugin;

use PHPCensor\Common\Application\ApplicationInterface;
use PHPCensor\Common\Build\BuildErrorWriterInterface;
use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\Common\Build\BuildLoggerInterface;
use PHPCensor\Common\Build\BuildMetaWriterInterface;
use PHPCensor\Common\CommandExecutorInterface;
use PHPCensor\Common\PathResolverInterface;
use PHPCensor\Common\Plugin\Plugin;
use PHPCensor\Common\Plugin\Plugin\ParameterBag;
use PHPCensor\Common\Project\ProjectInterface;
use PHPCensor\Common\VariableInterpolatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class SimplePlugin extends Plugin
{
    /**
     * {@inheritdoc}
     */
    public static function getName(): string
    {
        return 'simple_plugin';
    }

    /**
     * {@inheritdoc}
     */
    public function getBuild(): BuildInterface
    {
        return $this->build;
    }

    /**
     * {@inheritdoc}
     */
    public function getBuildSettings(): ParameterBag
    {
        return $this->buildSettings;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(): ParameterBag
    {
        return $this->options;
    }

    public function getBinaryNames(): array
    {
        return $this->binaryNames;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(): bool
    {
        return true;
    }
}

class SimplePluginWithBinaryNames extends SimplePlugin
{
    protected function getPluginDefaultBinaryNames(): array
    {
        return ['executable', 'executable.phar'];
    }
}

class PluginTest extends TestCase
{
    /**
     * @var MockObject | BuildInterface
     */
    private $build;

    /**
     * @var MockObject | ProjectInterface
     */
    private $project;

    /**
     * @var MockObject | ApplicationInterface
     */
    private $application;

    /**
     * @var MockObject | BuildLoggerInterface
     */
    private $buildLogger;

    /**
     * @var MockObject | BuildErrorWriterInterface
     */
    private $buildErrorWriter;

    /**
     * @var MockObject | BuildMetaWriterInterface
     */
    private $buildMetaWriter;

    /**
     * @var MockObject | CommandExecutorInterface
     */
    private $commandExecutor;

    /**
     * @var MockObject | VariableInterpolatorInterface
     */
    private $variableInterpolator;

    /**
     * @var MockObject | PathResolverInterface
     */
    private $pathResolver;

    /**
     * @var MockObject | ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $buildPath;

    public function setUp()
    {
        parent::setUp();

        $this->buildPath = \rtrim(
            \realpath(__DIR__ . '/../../data/Plugin/AbstractPluginTest/build_x'),
            '/\\'
        ) . '/';

        $this->build = $this->createMock(BuildInterface::class);
        $this->build
            ->method('getBuildPath')
            ->willReturn($this->buildPath);

        $this->project = $this->createMock(ProjectInterface::class);
        $this->project
            ->method('getConfig')
            ->willReturn([]);

        $this->variableInterpolator = $this->createMock(VariableInterpolatorInterface::class);
        $this->variableInterpolator
            ->method('interpolate')
            ->willReturnCallback(function ($string) {
                return \str_replace(['%BUILD_PATH%'], $this->buildPath, $string);
            });

        $this->buildLogger      = $this->createMock(BuildLoggerInterface::class);
        $this->buildErrorWriter = $this->createMock(BuildErrorWriterInterface::class);
        $this->buildMetaWriter  = $this->createMock(BuildMetaWriterInterface::class);
        $this->commandExecutor  = $this->createMock(CommandExecutorInterface::class);
        $this->pathResolver     = $this->createMock(PathResolverInterface::class);
        $this->application      = $this->createMock(ApplicationInterface::class);
        $this->container        = $this->createMock(ContainerInterface::class);
    }

    public function testConstructSuccess()
    {
        $plugin = new SimplePlugin(
            $this->build,
            $this->project,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->application,
            $this->container
        );

        $this->assertInstanceOf(SimplePlugin::class, $plugin);
        $this->assertInstanceOf(BuildInterface::class, $plugin->getBuild());
        $this->assertInstanceOf(ParameterBag::class, $plugin->getBuildSettings());
        $this->assertInstanceOf(ParameterBag::class, $plugin->getOptions());
    }

    public function testConstructSuccessWithDefaultBuildSettings()
    {
        $plugin = new SimplePlugin(
            $this->build,
            $this->project,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->application,
            $this->container
        );

        $this->assertEquals([], $plugin->getBuildSettings()->all());

        $this->project = $this->createMock(ProjectInterface::class);
        $this->project
            ->method('getConfig')
            ->willReturn([
                'build_settings' => [],
            ]);

        $plugin = new SimplePlugin(
            $this->build,
            $this->project,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->application,
            $this->container
        );

        $this->assertEquals([], $plugin->getBuildSettings()->all());
    }

    public function testConstructSuccessWithAlternativeBuildSettings()
    {
        $this->project = $this->createMock(ProjectInterface::class);
        $this->project
            ->method('getConfig')
            ->willReturn([
                'build_settings' => [
                    'option_1' => [
                        'file1.php',
                        'file2.php',
                    ],
                    'option_2' => true,
                ],
            ]);

        $plugin = new SimplePlugin(
            $this->build,
            $this->project,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->application,
            $this->container
        );

        $this->assertEquals([
            'option_1' => [
                'file1.php',
                'file2.php',
            ],
            'option_2' => true,
        ], $plugin->getBuildSettings()->all());
    }

    public function testConstructSuccessWithDefaultPluginOptions()
    {
        $plugin = new SimplePlugin(
            $this->build,
            $this->project,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->application,
            $this->container
        );

        $this->assertEquals([], $plugin->getOptions()->all());

        $this->project = $this->createMock(ProjectInterface::class);
        $this->project
            ->method('getConfig')
            ->willReturn([
                'simple_plugin' => [],
            ]);

        $plugin = new SimplePlugin(
            $this->build,
            $this->project,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->application,
            $this->container
        );

        $this->assertEquals([], $plugin->getOptions()->all());
    }

    public function testConstructSuccessWithAlternativePluginOptions()
    {
        $this->project = $this->createMock(ProjectInterface::class);
        $this->project
            ->method('getConfig')
            ->willReturn([
                'simple_plugin' => [
                    'directory'   => 'directory',
                    'binary_path' => 'binary_path',
                    'ignore'      => [
                        'foo',
                        'bar',
                    ],
                ],
            ]);

        $plugin = new SimplePlugin(
            $this->build,
            $this->project,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->application,
            $this->container
        );

        $this->assertEquals([
            'directory'   => 'directory',
            'binary_path' => 'binary_path',
            'ignore'      => [
                'foo',
                'bar',
            ],
        ], $plugin->getOptions()->all());

        $this->project = $this->createMock(ProjectInterface::class);
        $this->project
            ->method('getConfig')
            ->willReturn([
                'simple_plugin' => [
                    'directory'   => 235,
                    'binary_path' => false,
                    'ignore'      => 'foo',
                ],
            ]);

        $plugin = new SimplePlugin(
            $this->build,
            $this->project,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->application,
            $this->container
        );

        $this->assertEquals([
            'directory'   => 235,
            'binary_path' => false,
            'ignore'      => 'foo',
        ], $plugin->getOptions()->all());
    }

    public function testConstructSuccessWithEmptyBinaryNames()
    {
        $plugin = new SimplePlugin(
            $this->build,
            $this->project,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->application,
            $this->container
        );

        $this->assertEquals([], $plugin->getBinaryNames());
    }

    public function testConstructSuccessWithDefaultBinaryNames()
    {
        $plugin = new SimplePluginWithBinaryNames(
            $this->build,
            $this->project,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->application,
            $this->container
        );

        $this->assertEquals(['executable', 'executable.phar'], $plugin->getBinaryNames());
    }

    public function testConstructSuccessWithBinaryNamesString()
    {
        $this->project = $this->createMock(ProjectInterface::class);
        $this->project
            ->method('getConfig')
            ->willReturn([
                'simple_plugin' => [
                    'binary_name' => 'exec',
                ],
            ]);

        $plugin = new SimplePluginWithBinaryNames(
            $this->build,
            $this->project,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->application,
            $this->container
        );

        $this->assertEquals([
            'exec',
            'executable',
            'executable.phar',
        ], $plugin->getBinaryNames());

        $this->project = $this->createMock(ProjectInterface::class);
        $this->project
            ->method('getConfig')
            ->willReturn([
                'simple_plugin' => [
                    'binary_name' => false,
                ],
            ]);

        $plugin = new SimplePluginWithBinaryNames(
            $this->build,
            $this->project,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->application,
            $this->container
        );

        $this->assertEquals([
            'executable',
            'executable.phar',
        ], $plugin->getBinaryNames());
    }

    public function testConstructSuccessWithBinaryNamesArray()
    {
        $this->project = $this->createMock(ProjectInterface::class);
        $this->project
            ->method('getConfig')
            ->willReturn([
                'simple_plugin' => [
                    'binary_name' => [
                        'exec',
                        'exec.phar',
                        'executable.phar',
                        '',
                        false,
                    ],
                ],
            ]);

        $plugin = new SimplePluginWithBinaryNames(
            $this->build,
            $this->project,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->application,
            $this->container
        );

        $this->assertEquals([
            'exec',
            'exec.phar',
            'executable.phar',
            'executable',
        ], $plugin->getBinaryNames());
    }

    public function testGetNameSuccess()
    {
        $this->assertEquals('simple_plugin', SimplePlugin::getName());
    }

    public function testDefaultCanExecute()
    {
        $this->assertFalse(SimplePlugin::canExecute(BuildInterface::STAGE_SETUP, $this->build));
        $this->assertFalse(SimplePlugin::canExecute(BuildInterface::STAGE_DEPLOY, $this->build));
        $this->assertFalse(SimplePlugin::canExecute(BuildInterface::STAGE_SUCCESS, $this->build));
        $this->assertFalse(SimplePlugin::canExecute(BuildInterface::STAGE_FIXED, $this->build));
        $this->assertFalse(SimplePlugin::canExecute(BuildInterface::STAGE_COMPLETE, $this->build));
        $this->assertFalse(SimplePlugin::canExecute(BuildInterface::STAGE_FAILURE, $this->build));
        $this->assertFalse(SimplePlugin::canExecute(BuildInterface::STAGE_BROKEN, $this->build));
        $this->assertFalse(SimplePlugin::canExecute(BuildInterface::STAGE_TEST, $this->build));
    }
}
