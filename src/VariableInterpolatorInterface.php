<?php

declare(strict_types = 1);

namespace PHPCensor\Common;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface VariableInterpolatorInterface
{
    /**
     * @param string $string
     *
     * @return string
     */
    public function interpolate(string $string): string;
}
