<?php


namespace App\Command\Parsers;


class JsonParser implements FileParser
{
    public function parse(string $file): \Generator
    {
        set_error_handler(function() { }); // we omit the warning for missing files and just return
        $fileData = file_get_contents($file);
        restore_error_handler();
        if (!empty($fileData)) {
            $contents = json_decode($fileData, true);
            if (empty(json_last_error())) {
                foreach ($contents as $row) {
                    $product = [
                        'id' => $row['id'],
                        'price' => $row['price'],
                        'unit' => $row['unit'],
                    ];

                    yield $product;
                }
            }
        }
    }
}