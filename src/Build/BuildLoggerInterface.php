<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Build;

interface BuildLoggerInterface
{
    /**
     * @param string $message
     */
    public function logWarning(string $message);

    /**
     * @param string $message
     */
    public function logSuccess(string $message);

    /**
     * @param string $message
     */
    public function logDebug(string $message);

    /**
     * @param string $message
     */
    public function logNormal(string $message);

    /**
     * @param string          $message
     * @param \Exception|null $exception
     */
    public function logFailure(
        string $message,
        ?\Exception $exception = null
    );
}
