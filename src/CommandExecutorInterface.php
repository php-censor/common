<?php

declare(strict_types = 1);

namespace PHPCensor\Common;

use PHPCensor\Common\Exception\Exception;

interface CommandExecutorInterface
{
    public function executeCommand(...$params): bool;

    public function enableCommandOutput();

    public function disableCommandOutput();

    /**
     * @return string
     */
    public function getLastCommandOutput(): string;

    /**
     * @param array  $binary
     * @param string $binaryPath
     * @param array  $binaryName
     *
     * @return string
     *
     * @throws Exception
     */
    public function findBinary(
        array $binary,
        string $binaryPath = '',
        array $binaryName = []
    ): string;
}
