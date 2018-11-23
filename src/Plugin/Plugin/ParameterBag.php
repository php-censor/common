<?php

declare(strict_types = 1);

namespace PHPCensor\Common\Plugin\Plugin;

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
        return \array_key_exists($key, $this->parameters)
            ? $this->parameters[$key]
            : $default;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->parameters);
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
