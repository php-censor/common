<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Plugin;

use PHPCensor\Common\Build\BuildInterface;
use PHPCensor\Common\Exception\Exception;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface PluginInterface
{
    /**
     * @return string
     */
    public static function getName(): string;

    /**
     * @return bool
     *
     * @throws Exception
     */
    public function execute(): bool;

    /**
     * @param string         $stage
     * @param BuildInterface $build
     *
     * @return bool
     */
    public static function canExecute(string $stage, BuildInterface $build): bool;
}
