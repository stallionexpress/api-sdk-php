<?php

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Resources\File;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class FileTest extends TestCase
{
    /** @var array */
    private $pngFormat;
    /** @var array */
    private $jpegFormat;
    /** @var array */
    private $pdfFormat;
    /** @var StreamInterface */
    private $stream;
    /** @var string */
    private $base64data;
    /** @var string */
    private $path;


    public function setUp()
    {
        parent::setUp();

        $this->pngFormat = [
            'mime_type' => 'image/png',
            'extension' => 'png',
        ];

        $this->jpegFormat = [
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
        ];

        $this->pdfFormat = [
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
        ];

        $this->stream = $this->createMock(StreamInterface::class);
        $this->stream
            ->method('getContents')
            ->willReturn('This is some test data.');

        $this->base64data = base64_encode('This is some test data.');

        $this->path = tempnam(sys_get_temp_dir(), 'mytest_file') . '.pdf';
        file_put_contents($this->path, 'This is some test data.');
    }

    /** @test */
    public function testId()
    {
        $file = new File();

        $this->assertNull($file->getId());
        $this->assertEquals('4bquri', $file->setId('4bquri')->getId());
    }

    /** @test */
    public function testType()
    {
        $file = new File();

        $this->assertEquals('files', $file->getType());
    }

    /** @test */
    public function testResourceType()
    {
        $file = new File();

        $this->assertNull($file->getResourceType());
        $this->assertEquals('some-resource-type', $file->setResourceType('some-resource-type')->getResourceType());
    }

    /** @test */
    public function testFormats()
    {
        $file = new File();

        $this->assertEmpty($file->getFormats());
        $this->assertEquals([$this->jpegFormat], $file->addFormat($this->jpegFormat['mime_type'], $this->jpegFormat['extension'])->getFormats());

        // Setting an array of formats removes all other formats.
        $this->assertEquals([$this->pdfFormat, $this->pngFormat],
            $file->addFormat($this->jpegFormat['mime_type'], $this->jpegFormat['extension'])
                ->setFormats([$this->pdfFormat, $this->pngFormat])
                ->getFormats()
        );

        // Adding a format to an array of existing formats keeps the old ones.
        // Order of the formats is always pdf->png->jpeg.
        $this->assertEquals([$this->pdfFormat, $this->pngFormat, $this->jpegFormat],
            $file->setFormats([$this->pngFormat, $this->jpegFormat])
                ->addFormat($this->pdfFormat['mime_type'], $this->pdfFormat['extension'])
                ->getFormats()
        );
    }

    /** @test */
    public function testStream()
    {
        $file = new File();

        $this->assertNull($file->getStream());
        $this->assertEquals($this->stream, $file->setStream($this->stream, 'image/png')->getStream('image/png'));

        // Using getStream() without giving a mime_type loops over the
        // file's formats and returns first stream it encounters.
        $this->assertEquals($this->stream,
            $file->addFormat($this->pngFormat['mime_type'], $this->pngFormat['extension'])
                ->setStream($this->stream, 'image/png')
                ->getStream()
        );
    }

    /** @test */
    public function testBase64Data()
    {
        $file = new File();

        $this->assertNull($file->getBase64Data());
        $this->assertEquals($this->base64data,
            $file->setBase64Data($this->base64data, 'application/pdf')
                ->getBase64Data('application/pdf')
        );

        // Using getBase64Data() without giving a mime_type loops over the
        // file's formats and returns first base64Data it encounters.
        $this->assertEquals($this->base64data,
            $file->addFormat($this->pdfFormat['mime_type'], $this->pdfFormat['extension'])
                ->setBase64Data($this->base64data, 'image/png')
                ->getBase64Data()
        );

        // If no base64Data is set under the given mime_type,
        // getBase64Data will look for// data in other sources
        // (streams or temporary files).
        $this->assertEquals($this->base64data,
            $file->setStream($this->stream, 'image/jpeg')
                ->getBase64Data('image/jpeg')
        );
        $this->assertEquals($this->base64data,
            $file->setTemporaryFilePath($this->path, 'application/pdf')
                ->getBase64Data('application/pdf')
        );
    }

    /** @test */
    public function testTemporaryFilePath()
    {
        $file = new File();

        $this->assertNull($file->getTemporaryFilePath());
        $this->assertEquals($this->path,
            $file->setTemporaryFilePath($this->path, 'application/pdf')
                ->getTemporaryFilePath('application/pdf')
        );

//        $this->assertEquals($this->path,
//            $file->setStream())
    }
}