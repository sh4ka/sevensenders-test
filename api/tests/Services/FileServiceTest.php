<?php

namespace App\Services;


use App\Parsers\JsonParser;
use App\Parsers\XmlParser;
use App\ValueObjects\FileConfiguration;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FileServiceTest extends TestCase
{
    /**
     * @var FileConfiguration|MockObject $fileConfiguration
     */
    private $fileConfiguration;
    /**
     * @var XmlParser|MockObject $xmlParser
     */
    private $xmlParser;
    /**
     * @var JsonParser|MockObject $jsonParser
     */
    private $jsonParser;
    /**
     * @var vfsStreamDirectory
     */
    public $fileSystem;

    public function setUp()
    {
        $directory = [
            'json' => [
                'valid.json' => '[{
                    "id": "BA-01",
                    "price": {
                      "value": 2.45,
                      "currency": "EUR"
                    },
                    "unit": "piece"
                  }]',
                'invalid.json' => '{"test":123'
            ],
            'xml' => [
                'valid.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Products>
    <Product id="43b105a0-b5da-401b-a91d-32237ae418e4">
        <Name>Banana</Name>
        <Description>
            <![CDATA[
            <p>The <b>banana</b> is an edible
            ]]>
        </Description>
        <sku>BA-01</sku>
    </Product>
</Products>',
                'invalid.xml' => '<?xml vers'
            ]
        ];
        // setup and cache the virtual file system
        $this->fileSystem = vfsStream::setup('root', 444, $directory);

        $this->fileConfiguration = $this->createMock(FileConfiguration::class);

        $this->xmlParser = $this->createMock(XmlParser::class);
        $this->jsonParser = $this->createMock(JsonParser::class);
    }

    public function testGetProductsFromOutputData()
    {
        $this->fileConfiguration->expects($this->once())
            ->method('getOutputFilename')->willReturn('test'); // not empty, output file exists
        $this->fileConfiguration->expects($this->once())
            ->method('getOutputFilenameFullpath')->willReturn($this->fileSystem->url() . '/json/valid.json');

        $sut = new FileService($this->fileConfiguration, $this->xmlParser, $this->jsonParser);
        $result = $sut->getProducts();
        $this->assertTrue(is_array($result));
    }

    public function testGetProductsFromInputData()
    {
        $this->fileConfiguration->expects($this->once())
            ->method('getOutputFilename')->willReturn(null); // empty, use provided inputs
        $this->fileConfiguration->expects($this->once())
            ->method('getProductsInputFullpath')->willReturn($this->fileSystem->url() . '/xml/valid.xml');
        $this->fileConfiguration->expects($this->once())
            ->method('getPricesInputFullpath')->willReturn($this->fileSystem->url() . '/json/valid.xml');
        $this->xmlParser->expects($this->once())
            ->method('parse')->will($this->returnCallback(function () {
                $data = [
                    [
                        'sku' => 'test'
                    ]
                ];
                foreach ($data as $e) {
                    // return a generator
                    yield $e;
                }
            }));
        $this->jsonParser->expects($this->once())
            ->method('parse')->will($this->returnCallback(function () {
                $data = [
                    [
                        'id' => 'test',
                        'unit' => 'test'
                    ]
                ];
                foreach ($data as $e) {
                    // return a generator
                    yield $e;
                }
            }));

        $sut = new FileService($this->fileConfiguration, $this->xmlParser, $this->jsonParser);
        $result = $sut->getProducts();
        $this->assertTrue(is_array($result));

        // one could get very creative with the next assertions
        $this->assertTrue(isset($result['products']));
        $this->assertTrue(isset($result['products']['test']));
        $this->assertTrue($result['products']['test']['sku'] === 'test');
        $this->assertTrue(!empty($result['products']['test']['prices']));
    }

    public function testGetProductPriceFromOutputData()
    {
        $this->fileConfiguration->expects($this->once())
            ->method('getOutputFilename')->willReturn('test'); // not empty, output file exists
        $this->fileConfiguration->expects($this->once())
            ->method('getOutputFilenameFullpath')->willReturn($this->fileSystem->url() . '/json/valid.json');

        $sut = new FileService($this->fileConfiguration, $this->xmlParser, $this->jsonParser);
        $result = $sut->getProductPrice('test', 'test');
        $this->assertTrue(is_array($result));
    }

    public function testGetProductPriceFromInputData()
    {
        $this->fileConfiguration->expects($this->once())
            ->method('getOutputFilename')->willReturn(null); // empty, use provided inputs
        $this->fileConfiguration->expects($this->once())
            ->method('getProductsInputFullpath')->willReturn($this->fileSystem->url() . '/xml/valid.xml');
        $this->fileConfiguration->expects($this->once())
            ->method('getPricesInputFullpath')->willReturn($this->fileSystem->url() . '/json/valid.xml');
        $this->xmlParser->expects($this->once())
            ->method('parse')->will($this->returnCallback(function () {
                $data = [
                    [
                        'sku' => 'test'
                    ]
                ];
                foreach ($data as $e) {
                    // return a generator
                    yield $e;
                }
            }));
        $this->jsonParser->expects($this->once())
            ->method('parse')->will($this->returnCallback(function () {
                $data = [
                    [
                        'id' => 'test',
                        'unit' => 'test'
                    ]
                ];
                foreach ($data as $e) {
                    // return a generator
                    yield $e;
                }
            }));

        $sut = new FileService($this->fileConfiguration, $this->xmlParser, $this->jsonParser);
        $result = $sut->getProductPrice('test', 'test');
        $this->assertTrue(is_array($result));
    }
}
