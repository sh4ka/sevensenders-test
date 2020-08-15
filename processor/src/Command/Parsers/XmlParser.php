<?php


namespace App\Command\Parsers;


class XmlParser implements FileParser
{

    /**
     * @var \XMLReader $processor
     */
    protected $processor;
    protected $productNode = 'Product';

    public function __construct()
    {
        $this->processor = new \XMLReader();
    }

    public function parse(string $file): \Generator
    {
        if (empty($this->processor)) {
            return [];
        }

        $this->processor->open($file);

        while($this->processor->read() && $this->processor->name !== $this->productNode)
        {
            // this technique moves the cursor all along the document skipping all that is not a Product
            ;
        }

        while($this->processor->name === $this->productNode)
        {
            $element = new \SimpleXMLElement($this->processor->readOuterXML());

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

}