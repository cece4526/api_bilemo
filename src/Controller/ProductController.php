<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductService;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'product', methods: ['GET'])]
    public function getProductList(ProductService $productService, SerializerInterface $serializer): JsonResponse
    {
        $productList = $productService->getPaginatedProducts();
        $jsonProductList = $serializer->serialize($productList, 'json');
        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    
    #[Route('/api/products/{id}', name: 'product', methods: ['GET'])]
    public function getDetailProduct(Product $product, SerializerInterface $serializer) : JsonResponse 
    {
        $jsonProduct = $serializer->serialize($product, 'json');
        return new JsonResponse($jsonProduct, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
