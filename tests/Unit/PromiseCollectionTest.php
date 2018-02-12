<?php

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Collection\PromiseCollection;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use function GuzzleHttp\Promise\promise_for;

class PromiseCollectionTest extends TestCase
{
    /** @var callable */
    private $promiseCreator;

    /** @var callable */
    private $resourceCreator;

    /** @var int */
    private $pageNumber;

    /** @var int */
    private $pageSize;

    public function setUp()
    {
        parent::setUp();

        $this->promiseCreator = function ($pageNumber, $pageSize) {
            $this->pageNumber = $pageNumber;
            $this->pageSize = $pageSize;

            $response = $this->createMock(ResponseInterface::class);
            $response->method('getBody')->willReturn('{"data": "something something", "meta": {"total_records": 123}}');

            return promise_for($response);
        };

        $this->resourceCreator = function ($data) {
            $this->assertEquals('something something', $data);

            $start = ($this->pageNumber - 1) * $this->pageSize;
            $resources = [];
            for ($n = $start; $n < 123 && $n < $start + $this->pageSize; $n++) {
                $resources[] = (object)['id' => $n];
            }

            return $resources;
        };
    }

    /** @test */
    public function testGet()
    {
        $collection = new PromiseCollection($this->promiseCreator, $this->resourceCreator);

        $resources = $collection->offset(83)->limit(9)->get();
        $this->assertCount(9, $resources);
        $this->assertEquals(85, $resources[85]->id);
    }

    /** @test */
    public function testForeach()
    {
        $collection = new PromiseCollection($this->promiseCreator, $this->resourceCreator);

        foreach ($collection as $resource) {
            $this->assertGreaterThanOrEqual(0, $resource->id);
            $this->assertLessThanOrEqual(29, $resource->id);
        }
    }

    /** @test */
    public function testOffsetAndLimit()
    {
        $collection = new PromiseCollection($this->promiseCreator, $this->resourceCreator);

        $resources = $collection->offset(73)->limit(6)->get();
        $this->assertCount(6, $resources);

        array_walk($resources, function ($resource) {
            $this->assertGreaterThanOrEqual(73, $resource->id);
            $this->assertLessThanOrEqual(78, $resource->id);
        });
    }

    /** @test */
    public function testCount()
    {
        $collection = new PromiseCollection($this->promiseCreator, $this->resourceCreator);

        $this->assertEquals(123, $collection->count());
    }

    /** @test */
    public function testValid()
    {
        $collection = new PromiseCollection($this->promiseCreator, $this->resourceCreator);

        $this->assertTrue($collection->offset(65)->valid());
        $this->assertFalse($collection->offset(1512312)->valid());
    }

    /** @test */
    public function testKeyCurrentAndNext()
    {
        $collection = new PromiseCollection($this->promiseCreator, $this->resourceCreator);

        $this->assertEquals(99, $collection->offset(99)->key());

        $collection->next();
        $this->assertEquals(100, $collection->current()->id);

        $this->assertNull($collection->offset(123541243)->current());
    }
}
