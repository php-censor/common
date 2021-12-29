<?php

declare(strict_types=1);

namespace PHPCensor\Common;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
interface ViewInterface
{
    public function hasVariable(string $key): bool;

    /**
     * @return mixed
     */
    public function getVariable(string $key);

    /**
     * @param mixed $value
     */
    public function setVariable(string $key, $value): bool;

    public function setVariables(array $value): bool;

    public function render(): string;
}
