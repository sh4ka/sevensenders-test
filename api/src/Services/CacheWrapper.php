<?php

namespace App\Services;

use Symfony\Component\Cache\Adapter\AdapterInterface;

class CacheWrapper
{
    /** @var AdapterInterface $adapter */
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    public function getCacheAdapter(): AdapterInterface
    {
        return $this->adapter;
    }
}