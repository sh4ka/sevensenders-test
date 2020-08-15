<?php

namespace App\Services;

use App\Parsers\JsonParser;
use App\Parsers\XmlParser;
use App\ValueObjects\FileConfiguration;

/**
 * When designing an api like this there is always the question of creating entities for transactional
 * data like products or prices received from the files. For this exercise I have decided to not use them as
 * it adds a small amount of overhead to the response. That is why I am using the properties of the data I receive
 * directly.
 * Also this class can use 2 different ways of returning data, just checking if there is an output file present
 *
 * Class FileService
 * @package App\Services
 */
class FileService
{
    /**
     * @var FileConfiguration $fileConfiguration
     */
    private $fileConfiguration;
    /**
     * @var XmlParser $xmlParser
     */
    private $xmlParser;
    /**
     * @var JsonParser $jsonParser
     */
    private $jsonParser;

    private $outputDataProducts = [];

    public function __construct(FileConfiguration $fileConfiguration, XmlParser $xmlParser, JsonParser $jsonParser)
    {
        $this->fileConfiguration = $fileConfiguration;
        $this->xmlParser = $xmlParser;
        $this->jsonParser = $jsonParser;
    }

    public function getProducts(): array
    {
        $this->assembleOutputData();
        return $this->outputDataProducts;
    }

    public function getProduct($sku): array
    {
        $this->assembleOutputData();
        return $this->outputDataProducts['products'][$sku] ?? [];
    }

    public function getProductPrice($sku, $unit): array
    {
        $this->assembleOutputData();
        return $this->outputDataProducts['products'][$sku]['prices'][$unit] ?? [];
    }

    private function assembleOutputData(): bool
    {
        if (!empty($this->fileConfiguration->getOutputFilename())) {
            $this->setOutputDataFromProcess();

            return true;
        }
        $this->setXmlProductData();
        $this->setJsonPriceData();

        return true;
    }

    private function setOutputDataFromProcess(): void
    {
        $this->outputDataProducts = json_decode(
            file_get_contents($this->fileConfiguration->getOutputFilenameFullpath()),
            true
        );
    }

    private function setXmlProductData(): void
    {
        $this->outputDataProducts = ['products' => []];
        foreach ($this->xmlParser->parse($this->fileConfiguration->getProductsInputFullpath()) as $row) {
            $this->outputDataProducts['products'][$row['sku']] = $row;
        }
    }

    private function setJsonPriceData(): void
    {
        foreach ($this->jsonParser->parse($this->fileConfiguration->getPricesInputFullpath()) as $row) {
            $this->outputDataProducts['products'][$row['id']]['prices'][$row['unit']] = $row;
        }
    }
}