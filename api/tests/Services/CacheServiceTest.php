<?php

namespace App\Services;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CacheServiceTest extends TestCase
{
    /**
     * @var CacheWrapper
     */
    public $wrapper;

    /**
     * @var AdapterInterface
     */
    public $cache;

    public function setUp()
    {
        $this->wrapper = $this->createMock(CacheWrapper::class);
        $this->cache = $this->createMock(AdapterInterface::class);
    }

    /**
     * @param $key
     * @param $result
     * @param $expected
     *
     * @dataProvider cacheItemProvider
     */
    public function testGetItemCache($key, $cacheItem, $expected)
    {
        $this->cache->expects($this->once())
            ->method('getItem')->willReturn($cacheItem);
        $this->wrapper->expects($this->once())
            ->method('getCacheAdapter')->willReturn($this->cache);

        $sut = new CacheService($this->wrapper);
        $result = $sut->getItem($key);
        $this->assertTrue($result === $expected);
    }

    /**
     * @param $key
     * @param $cacheItem
     * @param $expected
     *
     * @dataProvider setCacheItemProvider
     */
    public function testSetItem($key, $value, $cacheItem, $expected)
    {
        $this->cache->expects($this->once())
            ->method('getItem')->willReturn($cacheItem);
        $this->cache->expects($this->once())
            ->method('save');
        $this->wrapper->expects($this->once())
            ->method('getCacheAdapter')->willReturn($this->cache);

        $sut = new CacheService($this->wrapper);
        $result = $sut->setItem($key, $value);
        $this->assertTrue($result === $expected);
    }

    public function cacheItemProvider()
    {
        return [
            [
                'test',
                $this->getCacheItemMock(true, '[]'),
                []
            ],
            [
                'test',
                $this->getCacheItemMock(false, '[]'),
                null
            ],
        ];
    }

    public function setCacheItemProvider()
    {
        return [
            [
                'test',
                '[]',
                $this->setCacheItemMock(),
                true
            ],
            [
                'test',
                [],
                $this->setCacheItemMock(),
                true
            ],
        ];
    }

    public function getCacheItemMock(bool $willHit, string $content)
    {
        $itemMock = $this->createMock(ItemInterface::class);
        $itemMock->expects($this->once())
            ->method('isHit')->willReturn($willHit);
        if ($willHit) {
            $itemMock->expects($this->once())
                ->method('get')->willReturn($content);
        }

        return $itemMock;
    }

    public function setCacheItemMock()
    {
        $itemMock = $this->createMock(ItemInterface::class);
        $itemMock->expects($this->once())
            ->method('set');

        return $itemMock;
    }
}
