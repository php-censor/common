<?php

declare(strict_types=1);

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
     * @return mixed|null
     */
    public function get(string $key, mixed $default = null);

    public function has(string $key): bool;

    public function all(): array;
}
