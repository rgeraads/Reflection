<?php

namespace phpDocumentor\SimpleFilter;

use \Mockery as m;

class ChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Chain
     */
    private $chain;

    public function setUp()
    {
        $this->chain = new Chain();
    }

    /**
     * @covers \phpDocumentor\SimpleFilter\Chain::attach
     */
    public function testAttachFilterProperty()
    {
        /** @var FilterInterface $filterMock */
        $filterMock = m::mock('phpDocumentor\SimpleFilter\FilterInterface');
        $chain = $this->chain->attach($filterMock);

        $this->assertInstanceOf('phpDocumentor\SimpleFilter\Chain', $chain);
        $this->assertSame(1, $chain->count());
    }

    /**
     * @covers \phpDocumentor\SimpleFilter\Chain::attach
     */
    public function testAttachCallbackProperty()
    {
        $callback = $this->chain->attach(function () {
        });
        $chain = $this->chain->attach($callback);

        $this->assertInstanceOf('phpDocumentor\SimpleFilter\Chain', $chain);
    }

    /**
     * @covers \phpDocumentor\SimpleFilter\Chain::attach
     *
     * @expectedException \InvalidArgumentException
     */
    public function testAttachInvalidProperty()
    {
        $this->chain->attach(null);
    }

    /**
     * @covers \phpDocumentor\SimpleFilter\Chain::filter
     */
    public function testFilterCallableProperty()
    {
        $value = function ($input) {
            return 'foo' . $input;
        };

        $this->chain->attach($value);
        $filter = $this->chain->filter('bar');

        $this->assertSame('foobar', $filter);
    }

    /**
     * @covers \phpDocumentor\SimpleFilter\Chain::filter
     *
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unable to process filter, one of the filters is not a FilterInterface or callback
     */
    public function testFilterInvalidProperty()
    {
        $chain = new \ReflectionClass($this->chain);
        $mockedQueue = new \SplPriorityQueue();
        $mockedQueue->insert('foo', 1000);

        $innerQueue = $chain->getProperty('innerQueue');
        $innerQueue->setAccessible(true);
        $innerQueue->setValue($this->chain, $mockedQueue);

        $this->chain->filter($innerQueue);
    }

    /**
     * @covers \phpDocumentor\SimpleFilter\Chain::count
     */
    public function testCountIncreasesAfterEachAttach()
    {
        $this->assertCount(0, $this->chain);

        for ($i = 1; $i < 5; $i++) {
            $this->chain->attach(function () {
            });
            $this->assertCount($i, $this->chain);
        }
    }

    /**
     * @covers \phpDocumentor\SimpleFilter\Chain::getIterator
     */
    public function testGetIterator()
    {
        $iterator = $this->chain->getIterator();
        $this->assertInstanceOf('SplPriorityQueue', $iterator);
    }
}
