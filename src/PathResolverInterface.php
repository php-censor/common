<?php

declare(strict_types = 1);

namespace PHPCensor\Common;

interface PathResolverInterface
{
    /**
     * @param string $pluginDirectory
     *
     * @return string
     */
    public function resolveDirectory(string $pluginDirectory): string;

    /**
     * @param string $pluginBinaryPath
     *
     * @return string
     */
    public function resolveBinaryPath(string $pluginBinaryPath): string;

    /**
     * @param string $path
     * @param bool   $isFile
     *
     * @return string
     */
    public function resolvePath(string $path, bool $isFile = false): string;

    /**
     * @param string[] $pluginIgnores
     * @param bool     $onlyInBuildPath
     *
     * @return string[]
     */
    public function resolveIgnores(array $pluginIgnores, bool $onlyInBuildPath = true): array;
}
