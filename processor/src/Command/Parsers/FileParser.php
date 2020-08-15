<?php

namespace App\Command\Parsers;

interface FileParser
{
    public function parse(string $file): \Generator;
}