<?php

declare(strict_types = 1);

namespace Tests\PHPCensor\Common\Plugin\Plugin;

use PHPCensor\Common\Plugin\Plugin\ParameterBag;
use PHPUnit\Framework\TestCase;

class ParameterBagTest extends TestCase
{
    public function testConstruct()
    {
        $parameterBag = new ParameterBag([]);

        $this->assertInstanceOf(\IteratorAggregate::class, $parameterBag);
        $this->assertInstanceOf(\Countable::class, $parameterBag);
        $this->assertInstanceOf(ParameterBag::class, $parameterBag);
    }

    public function testAll()
    {
        $parameterBag = new ParameterBag([
            'foo'   => 'bar',
            'foo_1' => [
                'foo_2' => 'bar_2',
            ],
            'null'   => null,
            'null_1' => [
                'null_2' => null,
            ],
        ]);

        $this->assertEquals([
            'foo'   => 'bar',
            'foo_1' => [
                'foo_2' => 'bar_2',
            ],
            'null'   => null,
            'null_1' => [
                'null_2' => null,
            ],
        ], $parameterBag->all());
    }

    public function testGet()
    {
        $parameterBag = new ParameterBag([
            'foo'   => 'bar',
            'foo_1' => [
                'foo_2' => 'bar_2',
            ],
            'null'   => null,
            'null_1' => [
                'null_2' => null,
            ],
        ]);

        $this->assertEquals('bar', $parameterBag->get('foo'));
        $this->assertEquals('bar_2', $parameterBag->get('foo_1.foo_2'));

        $this->assertEquals('default', $parameterBag->get('unknown', 'default'));
        $this->assertEquals('default_2', $parameterBag->get('foo_1.unknown', 'default_2'));

        $this->assertNull($parameterBag->get('unknown'));
        $this->assertNull($parameterBag->get('foo_1.unknown'));

        $this->assertNull($parameterBag->get('unknown', null));
        $this->assertNull($parameterBag->get('foo_1.unknown', null));

        $this->assertNull($parameterBag->get('null', 'default'));
        $this->assertNull($parameterBag->get('null_1.null_2', 'default_2'));
    }

    public function testHas()
    {
        $parameterBag = new ParameterBag([
            'foo'   => 'bar',
            'foo_1' => [
                'foo_2' => 'bar_2',
            ],
            'null'   => null,
            'null_1' => [
                'null_2' => null,
            ],
        ]);

        $this->assertTrue($parameterBag->has('foo'));
        $this->assertTrue($parameterBag->has('foo_1.foo_2'));

        $this->assertTrue($parameterBag->has('null'));
        $this->assertTrue($parameterBag->has('null_1.null_2'));

        $this->assertFalse($parameterBag->has('unknown'));
        $this->assertFalse($parameterBag->has('foo_1.unknown'));
    }

    public function testGetIterator()
    {
        $parameters = [
            'foo'   => 'bar',
            'foo_1' => [
                'foo_2' => 'bar_2',
            ],
            'null'   => null,
            'null_1' => [
                'null_2' => null,
            ],
        ];
        $parameterBag = new ParameterBag($parameters);

        $i = 0;
        foreach ($parameterBag as $key => $value) {
            ++$i;
            $this->assertEquals($parameters[$key], $value);
        }

        $this->assertEquals(\count($parameters), $i);
    }

    public function testCount()
    {
        $parameters = [
            'foo'   => 'bar',
            'foo_1' => [
                'foo_2' => 'bar_2',
            ],
            'null'   => null,
            'null_1' => [
                'null_2' => null,
            ],
        ];
        $parameterBag = new ParameterBag($parameters);

        $this->assertCount(\count($parameters), $parameterBag);
    }
}
