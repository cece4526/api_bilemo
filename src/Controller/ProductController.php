<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductService;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class ProductController extends AbstractController
{

    /**
     * This method allows you to recover all the products.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return the list of products",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="The page you want to retrieve",
     *     @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="The number of elements we want to recover",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Products")
     *
     * @param ProductService $productService
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cachePool
     * @return JsonResponse
     */
    #[Route('/api/products', name: 'products', methods: ['GET'])]
    public function getProductList(ProductService $productService, SerializerInterface $serializer,TagAwareCacheInterface $cachePool): JsonResponse
    {
        $productList = $productService->getPaginatedProducts();
        $idCache = "getAllProducts" .$productList->getCurrentPageNumber()."-". $productList->getItemNumberPerPage();
        $productList = $cachePool->get($idCache, function (ItemInterface $item) use ( $productList){
            $item->tag("productListCache");
            return $productList;
        });
        $jsonProductList = $serializer->serialize($productList, 'json');
        return new JsonResponse($jsonProductList, Response::HTTP_OK, [], true);
    }

    /**
     * this method returns the detail of a product.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return the detail of a product",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *     )
     * )
     * @OA\Tag(name="Products")
     *
     * @param Product $product
     * @param SerializerInterface $serializer
     * @param TagAwareCacheInterface $cachePool
     * @return JsonResponse
     */
    #[Route('/api/products/{id}', name: 'detailProduct', methods: ['GET'])]
    public function getDetailProduct(Product $product, SerializerInterface $serializer, TagAwareCacheInterface $cachePool) : JsonResponse 
    {
        $idCache = "getDetailProduct";
        $product = $cachePool->get($idCache, function (ItemInterface $item) use ( $product){
            $item->tag("productCache");
            $item->expiresAfter(1800);
            return $product;
        });
        $jsonProduct = $serializer->serialize($product, 'json');
        return new JsonResponse($jsonProduct, Response::HTTP_OK, ['accept' => 'json'], true);
    }
}
