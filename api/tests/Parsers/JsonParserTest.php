<?php

namespace App\Tests\Parsers;

use App\Parsers\JsonParser;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class JsonParserTest extends TestCase
{
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
            ]
        ];
        // setup and cache the virtual file system
        $this->fileSystem = vfsStream::setup('root', 444, $directory);
    }

    public function testParse()
    {
        $sut = new JsonParser();
        $result = $sut->parse($this->fileSystem->url() . '/json/valid.json');
        $this->assertTrue($result instanceof \Generator); // we get a generator
        foreach ($result as $row) {
            $this->assertTrue(is_array($row));
            $this->assertTrue($row['id'] === "BA-01");
            $this->assertTrue($row['price'] === ['value' => 2.45, 'currency' => 'EUR']);
            $this->assertTrue($row['unit'] === 'piece');
        }
    }

    public function testParseInvalidJson()
    {
        $sut = new JsonParser();
        $result = $sut->parse($this->fileSystem->url() . '/json/invalid.json');
        $this->assertTrue($result instanceof \Generator); // we get a generator
        // this generator is empty so we never break this test because we never go in it
        foreach ($result as $row) {
            $this->throwException(new \Exception('generator is not empty'));
        }
    }

    public function testWhenNoFile()
    {
        $sut = new JsonParser();
        $result = $sut->parse($this->fileSystem->url() . '/json/valid2.json'); // does not exist
        $this->assertTrue($result instanceof \Generator); // we get a generator
        // this generator is empty so we never break this test because we never go in it
        foreach ($result as $row) {
            $this->throwException(new \Exception('generator is not empty'));
        }
    }
}
