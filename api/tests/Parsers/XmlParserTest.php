<?php

namespace App\Parsers;


use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class XmlParserTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    public $fileSystem;

    /** @var \XMLReader|MockObject */
    public $processor;

    public function setUp()
    {
        $this->processor = $this->createMock(\XMLReader::class);

        $directory = [
            'xml' => [
                'valid.xml' => "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>
<Products>
    <Product id=\"43b105a0-b5da-401b-a91d-32237ae418e4\">
        <Name>Banana</Name>
        <Description>
            <![CDATA[
            <p>The <b>banana</b> is an edible
            ]]>
        </Description>
        <sku>BA-01</sku>
    </Product>
</Products>
",
                'invalid.xml' => '<?xml versio'
            ]
        ];
        // setup and cache the virtual file system
        $this->fileSystem = vfsStream::setup('root', 444, $directory);
    }

    public function testParse()
    {
        $file = $this->fileSystem->url() . '/xml/valid.xml';
        $sut = new XmlParser($this->processor, '');

        $this->processor->expects($this->any())
            ->method('readOuterXML')->willReturn(
                '<Product id="43b105a0-b5da-401b-a91d-32237ae418e4">
        <Name>Banana</Name>
        <Description>
            <![CDATA[
            <p>The <b>banana</b> is an edible
            ]]>
        </Description>
        <sku>BA-01</sku>
    </Product>'
            );

        $result = $sut->parse($file);
        $this->assertTrue($result instanceof \Generator);

        foreach ($result as $row) {
            $this->assertTrue($row['uid'] === '43b105a0-b5da-401b-a91d-32237ae418e4');
            $this->assertTrue($row['name'] === 'Banana');
            $this->assertTrue($row['sku'] === 'BA-01');
            return true; // stop generator
        }
    }

    public function testParseNonExistingFile()
    {
        $file = $this->fileSystem->url() . '/xml/nonexistent.xml';
        $sut = new XmlParser($this->processor, '');

        $this->processor->expects($this->any())
            ->method('open')->willThrowException(new \Exception('missing file'));

        $result = $sut->parse($file);
        $this->assertTrue($result instanceof \Generator);

        foreach ($result as $row) {
            $this->assertTrue($row['uid'] === '43b105a0-b5da-401b-a91d-32237ae418e4');
            $this->assertTrue($row['name'] === 'Banana');
            $this->assertTrue($row['sku'] === 'BA-01');
            return true; // stop generator
        }
    }
}
