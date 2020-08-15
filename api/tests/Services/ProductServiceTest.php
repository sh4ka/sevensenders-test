<?php

namespace App\Services;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ProductServiceTest extends TestCase
{

    /**
     * @param $sku
     * @param $unit
     * @param $expected
     *
     * @dataProvider productPriceProvider
     */
    public function testGetProductPriceCacheHit($sku, $unit, $expected)
    {
        /** @var CacheService|MockObject $cacheService */
        $cacheService = $this->createMock(CacheService::class);
        /** @var FileService|MockObject $fileService */
        $fileService = $this->createMock(FileService::class);

        $cacheService->expects($this->any())
            ->method('getItem')->with($sku.$unit)->willReturn($expected);
        $fileService->expects($this->any())
            ->method('getProductPrice')->with($sku, $unit)->willReturn($expected);

        $sut = new ProductService($cacheService, $fileService);
        $result = $sut->getProductPrice($sku, $unit);
        $this->assertTrue($result === $expected);
    }

    public function productPriceProvider()
    {
        return [
            [
                'test',
                'test',
                ['test']
            ],
            [
                '',
                'test',
                []
            ],
            [
                'test',
                '',
                []
            ],
            [
                'test',
                'test',
                []
            ],
        ];
    }
}
