<?php

namespace App\Parsers;

class XmlParser implements FileParser
{

    /**
     * @var \XMLReader $processor
     */
    protected $processor;
    protected $productNode = 'Product';

    public function __construct(\XMLReader $processor, string $productNode = 'Product')
    {
        $this->productNode = $productNode;
        $this->processor = $processor;
    }

    public function parse(string $file): \Generator
    {
        set_error_handler(function ($severity, $message, $file, $line) {
            // warning to exception as we do not want to just go ahead parsing a missing file
            throw new \ErrorException($message, $severity, $severity, $file, $line);
        });
        try {
            $this->processor->open($file);
        } catch (\Exception $e) {
            return [];
        }
        restore_error_handler();

        while($this->processor->read() && $this->getCurrentElementName() !== $this->productNode)
        {
            // this technique moves the cursor all along the document skipping all that is not a Product
            ;
        }

        while($this->getCurrentElementName() === $this->productNode)
        {
            $element = $this->getXmlRow($this->processor->readOuterXML());
            if (empty($element)) {
                return [];
            }

            $prod = [
                'uid' => strval($element->attributes()->id),
                'name' => strval($element->Name),
                'description' => strval($element->Description),
                'sku' => strval($element->sku)
            ];

            unset($element);
            $this->processor->next($this->productNode);

            yield $prod;
        }
    }

    /**
     * we could over engineer this part but for the sake of simplicity it is used directly
     */
    private function getCurrentElementName()
    {
        return $this->processor->name ?? '';
    }

    /**
     * we could over engineer this part but for the sake of simplicity it is used directly
     */
    private function getXmlRow(string $xmlRow)
    {
        return new \SimpleXMLElement($xmlRow);
    }

}