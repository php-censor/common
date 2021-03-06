<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Build;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface BuildMetaWriterInterface
{
    /**
     * @param int         $buildId
     * @param string|null $plugin
     * @param string      $key
     * @param mixed       $value
     */
    public function write(
        int $buildId,
        ?string $plugin,
        string $key,
        $value
    ): void;
}
