<?php

namespace App\Services;


use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class CacheWrapperTest extends TestCase
{
    /** @var AdapterInterface|MockObject $adapter */
    private $adapter;

    public function setUp()
    {
        $this->adapter = $this->createMock(AdapterInterface::class);
    }

    public function testGetCacheAdapter()
    {
        $sut = new CacheWrapper($this->adapter);
        $this->assertTrue($sut->getCacheAdapter() instanceof AdapterInterface);
    }
}
