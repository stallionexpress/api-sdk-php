<?php

namespace MyParcelCom\Sdk\Tests\Unit;

use MyParcelCom\Sdk\Resources\Customs;
use MyParcelCom\Sdk\Resources\Interfaces\CustomsInterface;
use MyParcelCom\Sdk\Resources\Interfaces\CustomsItemInterface;
use PHPUnit\Framework\TestCase;

class CustomsTest extends TestCase
{
    /** @test */
    public function testContentType()
    {
        $customs = new Customs();
        $this->assertEquals(CustomsInterface::CONTENT_TYPE_GIFTS, $customs->setContentType(CustomsInterface::CONTENT_TYPE_GIFTS)->getContentType());
    }

    /** @test */
    public function testInvoiceNumber()
    {
        $customs = new Customs();
        $this->assertEquals('Invoice#007', $customs->setInvoiceNumber('Invoice#007')->getInvoiceNumber());
    }

    /** @test */
    public function testItems()
    {
        $customs = new Customs();

        $this->assertEmpty($customs->getItems());

        $mock = $this->getMockClass(CustomsItemInterface::class);
        $items = [new $mock(), new $mock()];

        $customs->setItems($items);
        $this->assertCount(2, $customs->getItems());
        $this->assertEquals($items, $customs->getItems());

        $item = new $mock();
        $items[] = $item;
        $customs->addItem($item);
        $this->assertCount(3, $customs->getItems());
        $this->assertEquals($items, $customs->getItems());
    }

    /** @test */
    public function testNonDelivery()
    {
        $customs = new Customs();
        $this->assertEquals(CustomsInterface::NON_DELIVERY_ABANDON, $customs->setNonDelivery(CustomsInterface::NON_DELIVERY_ABANDON)->getNonDelivery());
    }

    /** @test */
    public function testIncoterm()
    {
        $customs = new Customs();
        $this->assertEquals(CustomsInterface::INCOTERM_DDP, $customs->setIncoterm(CustomsInterface::INCOTERM_DDP)->getIncoterm());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $item = $this->getMockBuilder(CustomsItemInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $item->method('jsonSerialize')
            ->willReturn([
                'sku'                 => '123456789',
                'description'         => 'OnePlus X',
                'item_value'          => [
                    'amount'   => 100,
                    'currency' => 'EUR',
                ],
                'quantity'            => 2,
                'hs_code'             => '8517.12.00',
                'origin_country_code' => 'GB',
            ]);

        $customs = (new Customs())
            ->setItems([$item])
            ->setIncoterm(CustomsInterface::INCOTERM_DDU)
            ->setNonDelivery(CustomsInterface::NON_DELIVERY_RETURN)
            ->setInvoiceNumber('NO.5')
            ->setContentType(CustomsInterface::CONTENT_TYPE_DOCUMENTS);

        $this->assertEquals([
            'content_type'   => 'documents',
            'invoice_number' => 'NO.5',
            'items'          => [
                [
                    'sku'                 => '123456789',
                    'description'         => 'OnePlus X',
                    'item_value'          => [
                        'amount'   => 100,
                        'currency' => 'EUR',
                    ],
                    'quantity'            => 2,
                    'hs_code'             => '8517.12.00',
                    'origin_country_code' => 'GB',
                ],
            ],
            'non_delivery'   => 'return',
            'incoterm'       => 'DDU',
        ], $customs->jsonSerialize());
    }
}
