<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Plugin\Plugin;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class ParameterBag implements \IteratorAggregate, \Countable
{
    /**
     * @var array
     */
    protected $parameters;

    /**
     * @param array $parameters
     */
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        if (\array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        }

        $parametersPart = $this->parameters;
        foreach (\explode('.', $key) as $keyPart) {
            if (\array_key_exists($keyPart, $parametersPart)) {
                $parametersPart = $parametersPart[$keyPart];
            } else {
                return $default;
            }
        }

        return $parametersPart;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        if (\array_key_exists($key, $this->parameters)) {
            return true;
        }

        $parametersPart = $this->parameters;
        foreach (\explode('.', $key) as $keyPart) {
            if (\array_key_exists($keyPart, $parametersPart)) {
                $parametersPart = $parametersPart[$keyPart];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->parameters;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->parameters);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return \count($this->parameters);
    }
}
