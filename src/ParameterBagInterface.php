<?php

declare(strict_types = 1);

namespace PHPCensor\Common;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface ParameterBagInterface
{
    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null);

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool;

    /**
     * @return array
     */
    public function all(): array;
}
