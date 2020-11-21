<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Build;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface BuildMetaInterface
{
    /**
     * @return string
     */
    public function getKey(): string;

    /**
     * @return mixed
     */
    public function getValue();
}
