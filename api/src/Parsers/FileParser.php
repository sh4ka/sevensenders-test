<?php

namespace App\Parsers;

interface FileParser
{
    public function parse(string $file): \Generator;
}