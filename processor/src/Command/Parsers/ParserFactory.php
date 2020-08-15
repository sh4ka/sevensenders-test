<?php


namespace App\Command\Parsers;

class ParserFactory
{
    public static function getParserInstance(string $fileExtension): FileParser
    {
        $desiredClass = 'App\Command\Parsers\\' . ucwords($fileExtension) . 'Parser';

        return new $desiredClass();
    }
}