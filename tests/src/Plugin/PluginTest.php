<?php

declare(strict_types = 1);

namespace Tests\PHPCensor\Common\Plugin;

use PHPCensor\Common\Build\BuildErrorWriterInterface;
use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\Common\Build\BuildLoggerInterface;
use PHPCensor\Common\Build\BuildMetaWriterInterface;
use PHPCensor\Common\CommandExecutorInterface;
use PHPCensor\Common\Exception\NotImplementedException;
use PHPCensor\Common\PathResolverInterface;
use PHPCensor\Common\Plugin\Plugin;
use PHPCensor\Common\Plugin\Plugin\ParameterBag;
use PHPCensor\Common\VariableInterpolatorInterface;
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

    /**
     * {@inheritdoc}
     */
    public function execute(): bool
    {
        return true;
    }
}

class SimplePluginWithName extends SimplePlugin
{
    /**
     * {@inheritdoc}
     */
    public static function getName(): string
    {
        return 'simple_plugin_with_name';
    }
}

class PluginTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|BuildInterface
     */
    private $build;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|BuildLoggerInterface
     */
    private $buildLogger;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|BuildErrorWriterInterface
     */
    private $buildErrorWriter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|BuildMetaWriterInterface
     */
    private $buildMetaWriter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CommandExecutorInterface
     */
    private $commandExecutor;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|VariableInterpolatorInterface
     */
    private $variableInterpolator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|PathResolverInterface
     */
    private $pathResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ContainerInterface
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
        $this->container        = $this->createMock(ContainerInterface::class);
    }

    public function testConstructSuccess()
    {
        $plugin = new SimplePluginWithName(
            $this->build,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->container,
            []
        );

        $this->assertInstanceOf(SimplePluginWithName::class, $plugin);
        $this->assertInstanceOf(BuildInterface::class, $plugin->getBuild());
        $this->assertInstanceOf(ParameterBag::class, $plugin->getBuildSettings());
        $this->assertInstanceOf(ParameterBag::class, $plugin->getOptions());
    }

    public function testConstructSuccessWithDefaultBuildSettings()
    {
        $plugin = new SimplePluginWithName(
            $this->build,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->container,
            []
        );

        $this->assertEquals([], $plugin->getBuildSettings()->all());

        $plugin = new SimplePluginWithName(
            $this->build,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->container,
            [
                'build_settings' => [],
            ]
        );

        $this->assertEquals([], $plugin->getBuildSettings()->all());
    }

    public function testConstructSuccessWithAlternativeBuildSettings()
    {
        $plugin = new SimplePluginWithName(
            $this->build,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->container,
            [
                'build_settings' => [
                    'option_1' => [
                        'file1.php',
                        'file2.php',
                    ],
                    'option_2' => true,
                ],
            ]
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
        $plugin = new SimplePluginWithName(
            $this->build,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->container,
            []
        );

        $this->assertEquals([], $plugin->getOptions()->all());

        $plugin = new SimplePluginWithName(
            $this->build,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->container,
            [
                'simple_plugin_with_name' => [],
            ]
        );

        $this->assertEquals([], $plugin->getOptions()->all());
    }

    public function testConstructSuccessWithAlternativePluginOptions()
    {
        $plugin = new SimplePluginWithName(
            $this->build,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->container,
            [
                'simple_plugin_with_name' => [
                    'directory'   => 'directory',
                    'binary_path' => 'binary_path',
                    'ignore'      => [
                        'foo',
                        'bar',
                    ],
                ],
            ]
        );

        $this->assertEquals([
            'directory'   => 'directory',
            'binary_path' => 'binary_path',
            'ignore'      => [
                'foo',
                'bar',
            ],
        ], $plugin->getOptions()->all());

        $plugin = new SimplePluginWithName(
            $this->build,
            $this->buildLogger,
            $this->buildErrorWriter,
            $this->buildMetaWriter,
            $this->commandExecutor,
            $this->variableInterpolator,
            $this->pathResolver,
            $this->container,
            [
                'simple_plugin_with_name' => [
                    'directory'   => 235,
                    'binary_path' => false,
                    'ignore'      => 'foo',
                ],
            ]
        );

        $this->assertEquals([
            'directory'   => 235,
            'binary_path' => false,
            'ignore'      => 'foo',
        ], $plugin->getOptions()->all());
    }

    public function testGetNameSuccess()
    {
        $this->assertEquals('simple_plugin_with_name', SimplePluginWithName::getName());
    }
}
