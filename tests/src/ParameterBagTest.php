<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Common;

use PHPCensor\Common\ParameterBag;
use PHPUnit\Framework\TestCase;

class ParameterBagTest extends TestCase
{
    public function testConstruct(): void
    {
        $parameterBag = new ParameterBag([]);

        $this->assertInstanceOf(\IteratorAggregate::class, $parameterBag);
        $this->assertInstanceOf(\Countable::class, $parameterBag);
        $this->assertInstanceOf(ParameterBag::class, $parameterBag);
    }

    public function testAll(): void
    {
        $parameters = [
            'foo'   => 'bar',
            'null'  => null,
            'foo_1' => [
                'foo_2'  => 'bar_2',
                'null_2' => null,
            ],
            'foo_3' => [
                'foo_4' => [
                    'foo_5'  => 'bar_5',
                    'null_3' => null,
                ],
            ],
        ];

        $parameterBag = new ParameterBag($parameters);

        $this->assertEquals($parameters, $parameterBag->all());
    }

    public function testGet(): void
    {
        $parameterBag = new ParameterBag([
            'foo'   => 'bar',
            'null'  => null,
            'foo_1' => [
                'foo_2'  => 'bar_2',
                'null_2' => null,
            ],
            'foo_3' => [
                'foo_4' => [
                    'foo_5'  => 'bar_5',
                    'null_5' => null,
                ],
            ],
        ]);

        $this->assertEquals('bar', $parameterBag->get('foo'));
        $this->assertEquals('bar_2', $parameterBag->get('foo_1.foo_2'));
        $this->assertEquals('bar_5', $parameterBag->get('foo_3.foo_4.foo_5'));

        $this->assertEquals('default', $parameterBag->get('unknown', 'default'));
        $this->assertEquals('default_2', $parameterBag->get('foo_1.unknown_2', 'default_2'));
        $this->assertEquals('default_5', $parameterBag->get('foo_3.foo_4.unknown_5', 'default_5'));

        $this->assertNull($parameterBag->get('unknown'));
        $this->assertNull($parameterBag->get('foo_1.unknown_2'));
        $this->assertNull($parameterBag->get('foo_3.foo_4.unknown_5'));

        $this->assertNull($parameterBag->get('null'));
        $this->assertNull($parameterBag->get('foo_1.null_2'));
        $this->assertNull($parameterBag->get('foo_3.foo_4.null_5'));

        $this->assertNull($parameterBag->get('null', 'default'));
        $this->assertNull($parameterBag->get('foo_1.null_2', 'default_2'));
        $this->assertNull($parameterBag->get('foo_3.foo_4.null_5', 'default_5'));
    }

    public function testHas(): void
    {
        $parameterBag = new ParameterBag([
            'foo'   => 'bar',
            'null'  => null,
            'foo_1' => [
                'foo_2'  => 'bar_2',
                'null_2' => null,
            ],
            'foo_3' => [
                'foo_4' => [
                    'foo_5'  => 'bar_5',
                    'null_5' => null,
                ],
            ],
        ]);

        $this->assertTrue($parameterBag->has('foo'));
        $this->assertTrue($parameterBag->has('foo_1.foo_2'));
        $this->assertTrue($parameterBag->has('foo_3.foo_4.foo_5'));

        $this->assertTrue($parameterBag->has('null'));
        $this->assertTrue($parameterBag->has('foo_1.null_2'));
        $this->assertTrue($parameterBag->has('foo_3.foo_4.null_5'));

        $this->assertFalse($parameterBag->has('unknown'));
        $this->assertFalse($parameterBag->has('foo_1.unknown_2'));
        $this->assertFalse($parameterBag->has('foo_3.foo_4.unknown_5'));
    }

    public function testGetIterator(): void
    {
        $parameters = [
            'foo'   => 'bar',
            'null'  => null,
            'foo_1' => [
                'foo_2'  => 'bar_2',
                'null_2' => null,
            ],
            'foo_3' => [
                'foo_4' => [
                    'foo_5'  => 'bar_5',
                    'null_5' => null,
                ],
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

    public function testCount(): void
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
