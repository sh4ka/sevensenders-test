<?php


namespace App\ValueObjects;


class FileConfiguration
{
    private $inputLocation;
    private $outputLocation;
    private $outputFilename;
    private $outputFilenameExtension;

    private const TREE_PATH = '/../../';
    private const SKIP = ['..', '.', '.gitkeep'];

    /**
     * These variables are being given by the exercise but this is easily configurable, I just do not
     * want to pollute the constructor anymore.
    */
    private const INPUT_PRODUCTS = 'products.xml';
    private const INPUT_PRICES = 'prices.json';

    public function __construct(
        string $inputLocation,
        string $outputLocation,
        string $outputFilenameExtension = 'json'
    )
    {
        $this->inputLocation = __DIR__ . self::TREE_PATH . $inputLocation;
        $this->outputLocation = __DIR__ . self::TREE_PATH . $outputLocation;
        $this->outputFilenameExtension = $outputFilenameExtension;
        $this->setOutputFilename();
    }

    public function getOutputFilenameExtension()
    {
        return $this->outputFilenameExtension;
    }

    public function getOutputDirectory()
    {
        return $this->outputLocation;
    }

    public function getOutputFilename()
    {
        return $this->outputFilename;
    }

    public function getOutputFilenameFullpath()
    {
        return $this->outputLocation . DIRECTORY_SEPARATOR . $this->outputFilename;
    }

    public function getProductsInputFullpath()
    {
        return $this->inputLocation . DIRECTORY_SEPARATOR . self::INPUT_PRODUCTS;
    }

    public function getPricesInputFullpath()
    {
        return $this->inputLocation . DIRECTORY_SEPARATOR . self::INPUT_PRICES;
    }

    /**
     * I wanted to make it possible to use a merged file for faster read access
     * but this can be automatically decided just by not having data in the output directory,
     * this way the operation is exactly the same.
     */
    private function setOutputFilename()
    {
        // check if there is a file there with the desired extension to control the behavior
        $files = array_diff(scandir($this->getOutputDirectory()), self::SKIP);
        $needle = $this->getOutputFilenameExtension();
        $found = array_filter($files, function ($element) use ($needle) {
            if (strstr($element, $needle)) {
                return true;
            }

            return false;
        });

        if (!empty($found)) {
            $this->outputFilename = array_pop($found);
        }
    }
}