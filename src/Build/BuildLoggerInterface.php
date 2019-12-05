<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Build;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface BuildLoggerInterface
{
    /**
     * @param string $message
     */
    public function logWarning(string $message): void;

    /**
     * @param string $message
     */
    public function logSuccess(string $message): void;

    /**
     * @param string $message
     */
    public function logDebug(string $message): void;

    /**
     * @param string $message
     */
    public function logNormal(string $message): void;

    /**
     * @param string          $message
     * @param \Exception|null $exception
     */
    public function logFailure(
        string $message,
        ?\Exception $exception = null
    ): void;
}
