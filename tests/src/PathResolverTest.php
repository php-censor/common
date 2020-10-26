<?php

declare(strict_types = 1);

namespace Tests\PHPCensor\Common;

use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\Common\Build\BuildLoggerInterface;
use PHPCensor\Common\PathResolver;
use PHPCensor\Common\PathResolverInterface;
use PHPCensor\Common\VariableInterpolatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PathResolverTest extends TestCase
{
    /**
     * @var MockObject | BuildInterface
     */
    private $build;

    /**
     * @var MockObject | BuildLoggerInterface
     */
    private $buildLogger;

    /**
     * @var MockObject | VariableInterpolatorInterface
     */
    private $variableInterpolator;

    /**
     * @var string
     */
    private $buildPath;

    /**
     * @var string
     */
    private $alternativeBuildPath;

    /**
     * @var string
     */
    private $rootDirectory;

    public function setUp()
    {
        parent::setUp();

        $this->rootDirectory = \rtrim(
            \realpath(__DIR__ . '/../../'),
            '/\\'
        ) . '/';

        $this->buildPath = \rtrim(
            \realpath(__DIR__ . '/../data/build1'),
            '/\\'
        ) . '/';

        $this->alternativeBuildPath = \rtrim(
            \realpath(__DIR__ . '/../data/build2'),
            '/\\'
        ) . '/';

        $this->build = $this->createMock(BuildInterface::class);
        $this->build
            ->method('getBuildPath')
            ->willReturn($this->buildPath);

        $this->buildLogger = $this->createMock(BuildLoggerInterface::class);

        $this->variableInterpolator = $this->createMock(VariableInterpolatorInterface::class);
        $this->variableInterpolator
            ->method('interpolate')
            ->willReturnArgument(0);
    }

    public function testConstruct()
    {
        $pathResolver = new PathResolver(
            $this->build,
            $this->buildLogger,
            $this->variableInterpolator,
            [
                'build_settings' => [
                    'directory' => 'example_directory',
                ],
            ]
        );

        self::assertInstanceOf(PathResolverInterface::class, $pathResolver);
        self::assertInstanceOf(PathResolver::class, $pathResolver);
    }

    public function testResolveDirectory_EmptyBuildDirectory_RelativePluginDirectory()
    {
        $pathResolver = new PathResolver(
            $this->build,
            $this->buildLogger,
            $this->variableInterpolator,
            []
        );

        self::assertEquals(
            $this->buildPath,
            $pathResolver->resolveDirectory('')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/',
            $pathResolver->resolveDirectory('../../data')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/',
            $pathResolver->resolveDirectory('../../data/')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/src/',
            $pathResolver->resolveDirectory('/../../src')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolveDirectory('directory1/../directory2')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolveDirectory('directory1\..\directory2\\')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolveDirectory('directory1/../////directory2')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolveDirectory('./directory1')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolveDirectory('./directory1/')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolveDirectory('/./directory2/')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/subdirectory1/',
            $pathResolver->resolveDirectory('./directory1/././subdirectory1')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolveDirectory('directory1')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolveDirectory('directory1/')
        );
    }

    public function testResolveDirectory_RelativeBuildDirectory()
    {
        $pathResolver = new PathResolver(
            $this->build,
            $this->buildLogger,
            $this->variableInterpolator,
            [
                'build_settings' => [
                    'directory' => '/../../data/build2',
                ],
            ]
        );

        self::assertEquals(
            $this->alternativeBuildPath,
            $pathResolver->resolveDirectory('')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolveDirectory('../../data/build1/directory1')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolveDirectory($this->rootDirectory . 'tests/data/build1/directory2')
        );
    }

    public function testResolveDirectory_EmptyBuildDirectory_AbsolutePluginDirectory()
    {
        $pathResolver = new PathResolver(
            $this->build,
            $this->buildLogger,
            $this->variableInterpolator,
            []
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build2/',
            $pathResolver->resolveDirectory(__DIR__ . '/../data/build2')
        );

        self::assertEquals(
            '/directory1/directory3/',
            $pathResolver->resolveDirectory('/directory1/directory2/../directory3')
        );

        self::assertEquals(
            '/directory1/directory3/',
            $pathResolver->resolveDirectory('\directory1\directory2\..\directory3')
        );

        self::assertEquals(
            '/directory1/',
            $pathResolver->resolveDirectory('///directory1')
        );
    }

    public function testResolveDirectory_AbsoluteBuildDirectory()
    {
        $pathResolver = new PathResolver(
            $this->build,
            $this->buildLogger,
            $this->variableInterpolator,
            [
                'build_settings' => [
                    'directory' => $this->rootDirectory . 'tests/data/./build2/',
                ],
            ]
        );

        self::assertEquals(
            $this->alternativeBuildPath,
            $pathResolver->resolveDirectory('')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolveDirectory('../../data/build1/directory1')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolveDirectory($this->rootDirectory . 'tests/data/build1/directory2')
        );
    }

    public function testResolveBinaryPath_EmptyBuildBinaryPath_RelativePluginBinaryPath()
    {
        $pathResolver = new PathResolver(
            $this->build,
            $this->buildLogger,
            $this->variableInterpolator,
            []
        );

        self::assertEquals(
            $this->buildPath,
            $pathResolver->resolveBinaryPath('')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/',
            $pathResolver->resolveBinaryPath('../../data')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/',
            $pathResolver->resolveBinaryPath('../../data/')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/src/',
            $pathResolver->resolveBinaryPath('/../../src')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolveBinaryPath('directory1/../directory2')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolveBinaryPath('directory1\..\directory2\\')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolveBinaryPath('directory1/../////directory2')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolveBinaryPath('./directory1')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolveBinaryPath('./directory1/')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolveBinaryPath('/./directory2/')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/subdirectory1/',
            $pathResolver->resolveBinaryPath('./directory1/././subdirectory1')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolveBinaryPath('directory1')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolveBinaryPath('directory1/')
        );
    }

    public function testResolveBinaryPath_RelativeBuildBinaryPath()
    {
        $pathResolver = new PathResolver(
            $this->build,
            $this->buildLogger,
            $this->variableInterpolator,
            [
                'build_settings' => [
                    'binary_path' => '/../../data/build2',
                ],
            ]
        );

        self::assertEquals(
            $this->alternativeBuildPath,
            $pathResolver->resolveBinaryPath('')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolveBinaryPath('../../data/build1/directory1')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolveBinaryPath($this->rootDirectory . 'tests/data/build1/directory2')
        );
    }

    public function testResolveBinaryPath_EmptyBuildBinaryPath_AbsolutePluginBinaryPath()
    {
        $pathResolver = new PathResolver(
            $this->build,
            $this->buildLogger,
            $this->variableInterpolator,
            []
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build2/',
            $pathResolver->resolveBinaryPath(__DIR__ . '/../data/build2')
        );

        self::assertEquals(
            '/directory1/directory3/',
            $pathResolver->resolveBinaryPath('/directory1/directory2/../directory3')
        );

        self::assertEquals(
            '/directory1/directory3/',
            $pathResolver->resolveBinaryPath('\directory1\directory2\..\directory3')
        );

        self::assertEquals(
            '/directory1/',
            $pathResolver->resolveBinaryPath('///directory1')
        );
    }

    public function testResolveBinaryPath_AbsoluteBuildBinaryPath()
    {
        $pathResolver = new PathResolver(
            $this->build,
            $this->buildLogger,
            $this->variableInterpolator,
            [
                'build_settings' => [
                    'binary_path' => $this->rootDirectory . 'tests/data/./build2/',
                ],
            ]
        );

        self::assertEquals(
            $this->alternativeBuildPath,
            $pathResolver->resolveBinaryPath('')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolveBinaryPath('../../data/build1/directory1')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolveBinaryPath($this->rootDirectory . 'tests/data/build1/directory2')
        );
    }

    public function testResolvePath_RelativePath()
    {
        $pathResolver = new PathResolver(
            $this->build,
            $this->buildLogger,
            $this->variableInterpolator,
            []
        );

        self::assertEquals(
            $this->buildPath,
            $pathResolver->resolvePath('')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/',
            $pathResolver->resolvePath('../../data')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/',
            $pathResolver->resolvePath('../../data/')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/src/',
            $pathResolver->resolvePath('/../../src')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolvePath('directory1/../directory2')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolvePath('directory1\..\directory2\\')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolvePath('directory1/../////directory2')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolvePath('./directory1')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolvePath('./directory1/')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory2/',
            $pathResolver->resolvePath('/./directory2/')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/subdirectory1/',
            $pathResolver->resolvePath('./directory1/././subdirectory1')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolvePath('directory1')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/',
            $pathResolver->resolvePath('directory1/')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build1/directory1/.gitkeep',
            $pathResolver->resolvePath('directory1/.gitkeep', true)
        );
    }

    public function testResolvePath_AbsolutePath()
    {
        $pathResolver = new PathResolver(
            $this->build,
            $this->buildLogger,
            $this->variableInterpolator,
            []
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build2/',
            $pathResolver->resolveDirectory(__DIR__ . '/../data/build2')
        );

        self::assertEquals(
            $this->rootDirectory . 'tests/data/build2/.gitkeep',
            $pathResolver->resolvePath(__DIR__ . '/../data/build2/.gitkeep', true)
        );

        self::assertEquals(
            '/directory1/directory3/',
            $pathResolver->resolveDirectory('/directory1/directory2/../directory3')
        );

        self::assertEquals(
            '/directory1/directory3/',
            $pathResolver->resolveDirectory('\directory1\directory2\..\directory3')
        );

        self::assertEquals(
            '/directory1/',
            $pathResolver->resolveDirectory('///directory1')
        );
    }

    public function testResolveIgnores_EmptyBuildIgnores_OnlyInBuildPath()
    {
        $pathResolver = new PathResolver(
            $this->build,
            $this->buildLogger,
            $this->variableInterpolator,
            []
        );

        self::assertEquals(
            [],
            $pathResolver->resolveIgnores([])
        );

        self::assertEquals(
            [
                0 => 'directory2',
                1 => 'directory1',
                2 => 'directory1/subdirectory1',
                3 => 'directory2/.gitkeep',
            ],
            $pathResolver->resolveIgnores([
                '',
                '../../data',
                '../../data/',
                '/../../src',
                'directory1/../directory2',
                'directory1\..\directory2\\',
                'directory1/../////directory2',
                './directory1',
                './directory1/',
                '/./directory2/',
                './directory1/././subdirectory1',
                'directory1',
                'directory1/',
                __DIR__ . '/../data/build2',
                '/directory1/directory2/../directory3',
                '\directory1\directory2\..\directory3',
                '///directory1',
                'directory2/.gitkeep',
            ])
        );
    }

    public function testResolveIgnores_EmptyBuildIgnores_NotOnlyInBuildPath()
    {
        $pathResolver = new PathResolver(
            $this->build,
            $this->buildLogger,
            $this->variableInterpolator,
            []
        );

        self::assertEquals(
            [
                0 => $this->rootDirectory . 'tests/data',
                1 => $this->rootDirectory . 'tests/src',
                2 => 'directory2',
                3 => 'directory1',
                4 => 'directory1/subdirectory1',
                5 => $this->rootDirectory . 'tests/data/build2',
                6 => '/directory1/directory3',
                7 => '/directory1',
                8 => 'directory2/.gitkeep',
            ],
            $pathResolver->resolveIgnores([
                '',
                '../../data',
                '../../data/',
                '/../../src',
                'directory1/../directory2',
                'directory1\..\directory2\\',
                'directory1/../////directory2',
                './directory1',
                './directory1/',
                '/./directory2/',
                './directory1/././subdirectory1',
                'directory1',
                'directory1/',
                __DIR__ . '/../data/build2',
                '/directory1/directory2/../directory3',
                '\directory1\directory2\..\directory3',
                '///directory1',
                'directory2/.gitkeep',
            ], false)
        );
    }

    public function testResolveIgnores_NotEmptyBuildIgnores_OnlyInBuildPath()
    {
        $pathResolver = new PathResolver(
            $this->build,
            $this->buildLogger,
            $this->variableInterpolator,
            [
                'build_settings' => [
                    'ignore' => [
                        '../../data',
                        '/./directory1/',
                        'directory1',
                    ],
                ],
            ]
        );

        self::assertEquals(
            [
                0 => 'directory1',
                1 => 'directory2',
                2 => 'directory2/.gitkeep',
            ],
            $pathResolver->resolveIgnores([
                '',
                '../../data',
                'directory1/../directory2',
                '/./directory2/',
                '/directory1/directory2/../directory3',
                'directory2/.gitkeep',
            ])
        );
    }

    public function testResolveIgnores_NotEmptyBuildIgnores_NotOnlyInBuildPath()
    {
        $pathResolver = new PathResolver(
            $this->build,
            $this->buildLogger,
            $this->variableInterpolator,
            [
                'build_settings' => [
                    'ignore' => [
                        '../../data',
                        '/./directory1/',
                        'directory1',
                    ],
                ],
            ]
        );

        self::assertEquals(
            [
                0 => $this->rootDirectory . 'tests/data',
                1 => 'directory1',
                2 => 'directory2',
                3 => '/directory1/directory3',
                4 => 'directory2/.gitkeep',
            ],
            $pathResolver->resolveIgnores([
                '',
                '../../data',
                'directory1/../directory2',
                '/./directory2/',
                '/directory1/directory2/../directory3',
                'directory2/.gitkeep',
            ], false)
        );
    }
}
