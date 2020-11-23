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
    public const KEY_DATA     = 'data';
    public const KEY_META     = 'meta';
    public const KEY_ERRORS   = 'errors';
    public const KEY_WARNINGS = 'warnings';
    public const KEY_COVERAGE = 'coverage';
    public const KEY_SUMMARY  = 'summary';

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
