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
            'hello' => 'world',
        ]);

        $this->assertEquals([
            'foo'   => 'bar',
            'hello' => 'world',
        ], $parameterBag->all());
    }

    public function testGet()
    {
        $parameterBag = new ParameterBag([
            'foo'  => 'bar',
            'null' => null,
        ]);

        $this->assertEquals('bar', $parameterBag->get('foo'));
        $this->assertEquals('default', $parameterBag->get('unknown', 'default'));
        $this->assertNull($parameterBag->get('unknown'));
        $this->assertNull($parameterBag->get('null', 'default'));
    }

    public function testHas()
    {
        $parameterBag = new ParameterBag([
            'foo'  => 'bar',
            'null' => null,
        ]);

        $this->assertTrue($parameterBag->has('foo'));
        $this->assertFalse($parameterBag->has('bar'));
    }

    public function testGetIterator()
    {
        $parameters = [
            'foo'   => 'bar',
            'hello' => 'world',
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
            'hello' => 'world',
        ];
        $parameterBag = new ParameterBag($parameters);

        $this->assertCount(\count($parameters), $parameterBag);
    }
}
