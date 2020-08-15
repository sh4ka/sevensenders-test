<?php

namespace App\Services;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CacheService
{
    /** @var AdapterInterface  */
    private $cache;
    
    public function __construct(CacheWrapper $cache)
    {
        $this->cache = $cache->getCacheAdapter();
    }

    public function getItem(string $key): ?array
    {
        /** @var ItemInterface $result */
        $result = $this->cache->getItem($key);
        if ($result->isHit()) {
            return json_decode($result->get(), true);
        }

        return null;
    }

    public function setItem(string $key, $value): bool
    {
        if (!is_string($value)) {
            $value = json_encode($value);
        }
        /** @var ItemInterface $result */
        $result = $this->cache->getItem($key);
        $result->set($value);
        $this->cache->save($result);

        return true;
    }
}