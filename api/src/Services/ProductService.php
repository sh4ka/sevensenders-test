<?php


namespace App\Services;

/**
 * The idea behind this class it that it should be used to fetch the data for the products, it will have a
 * dependency for a cache class
 *
 * Class ProductService
 * @package App\Services
 */
class ProductService
{
    private $cacheService;
    private $fileService;

    private const PRODUCTS_CACHE_KEY = 'products';

    public function __construct(CacheService $cacheService, FileService $fileService)
    {
        $this->cacheService = $cacheService;
        $this->fileService = $fileService;
    }

    public function getProducts(): ?array
    {
        $products = $this->cacheService->getItem(self::PRODUCTS_CACHE_KEY);
        if (empty($products)) {
            $products = $this->fileService->getProducts();
            // we only cache if there is actual data to cache
            if(!empty($products)) {
                $this->cacheService->setItem(self::PRODUCTS_CACHE_KEY, $products);
            }
        }

        return $products;
    }

    public function getProduct(string $sku)
    {
        if (empty($sku)) {
            return [];
        }

        $product = $this->cacheService->getItem($sku);
        if (empty($product)) {
            $product = $this->fileService->getProduct($sku);
            // we only cache if there is actual data to cache
            if(!empty($product)) {
                $this->cacheService->setItem($sku, $product);
            }
        }

        return $product;
    }

    public function getProductPrice(string $sku, string $unit)
    {
        if (empty($sku) || empty($unit)) {
            return [];
        }

        $product = $this->cacheService->getItem($sku.$unit);
        if (empty($product)) {
            $product = $this->fileService->getProductPrice($sku, $unit);
            // we only cache if there is actual data to cache
            if(!empty($product)) {
                $this->cacheService->setItem($sku.$unit, $product);
            }
        }

        return $product;
    }
}