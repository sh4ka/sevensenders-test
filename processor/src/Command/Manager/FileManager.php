<?php

namespace App\Command\Manager;

class FileManager
{
    private $inputLocation;
    private $outputLocation;

    private $directoryRecursion = '/../../../';

    public function __construct(string $inputLocation, string $outputLocation)
    {
        $this->inputLocation = __DIR__ . $this->directoryRecursion . $inputLocation;
        $this->outputLocation = __DIR__ . $this->directoryRecursion . $outputLocation;
    }

    public function areLocationsValid(): bool
    {
        if (!is_dir($this->inputLocation) ||
            !is_dir($this->outputLocation)) {
            return false;
        }

        return true;
    }

    public function getFilesInInput(): ?array
    {
        return array_diff(scandir($this->inputLocation), ['..', '.']);
    }

    public function getInputFile(string $filename)
    {
        return $this->inputLocation . DIRECTORY_SEPARATOR . $filename;
    }

    public function getExtension(string $file)
    {
        return pathinfo($file, PATHINFO_EXTENSION);
    }

    /**
     * todo: This method can take a lot of memory because it needs a full array. This should be refactored to use SAX but that is
     * beyond the scope of this test
     *
     * @param array $contents
     */
    public function writeOutputFile(array $contents)
    {
        $fp = fopen($this->outputLocation . DIRECTORY_SEPARATOR . 'output.json', 'w');
        fwrite($fp, json_encode($contents));
        fclose($fp);
    }
}