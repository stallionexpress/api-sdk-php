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
}
