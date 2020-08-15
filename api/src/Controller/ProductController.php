<?php

namespace App\Controller;

use App\Services\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product_list")
     */
    public function list(ProductService $productService)
    {
        $products = $productService->getProducts();
        return $this->json($products, Response::HTTP_OK);
    }

    /**
     * @Route("/product/{sku}", name="product_find")
     */
    public function find(ProductService $productService, $sku)
    {
        $product = $productService->getProduct($sku);
        return $this->json($product, Response::HTTP_OK);
    }

    /**
     * @Route("/product/{sku}/price", name="product_price")
     */
    public function price(Request $request, ProductService $productService, $sku)
    {
        $unit = $request->get('unit');
        $product = $productService->getProductPrice($sku, $unit);
        return $this->json($product, Response::HTTP_OK);
    }

}