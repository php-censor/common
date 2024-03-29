<?php

declare(strict_types=1);

namespace PHPCensor\Common\Build;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface BuildMetaWriterInterface
{
    public function write(
        BuildInterface $build,
        ?string $plugin,
        string $key,
        mixed $value
    ): void;
}
