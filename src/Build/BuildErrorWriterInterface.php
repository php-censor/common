<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Build;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface BuildErrorWriterInterface
{
    /**
     * @param int            $buildId
     * @param string         $plugin
     * @param string         $message
     * @param int            $severity
     * @param string|null    $file
     * @param int|null       $lineStart
     * @param int|null       $lineEnd
     * @param \DateTime|null $createdDate
     */
    public function write(
        int $buildId,
        string $plugin,
        string $message,
        int $severity,
        ?string $file = null,
        ?int $lineStart = null,
        ?int $lineEnd = null,
        ?\DateTime $createdDate = null
    ): void;
}
