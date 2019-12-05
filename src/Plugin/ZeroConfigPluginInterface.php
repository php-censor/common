<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Plugin;

use PHPCensor\Common\Build\BuildInterface;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface ZeroConfigPluginInterface
{
    /**
     * @param string         $stage
     * @param BuildInterface $build
     *
     * @return bool
     */
    public static function canExecuteOnStage(string $stage, BuildInterface $build): bool;
}
