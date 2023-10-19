<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Knp\Component\Pager\PaginatorInterface;

class ProductService 
{
    private RequestStack $requestStack;
    private ProductRepository $productRepo;
    private PaginatorInterface $paginator;
    
    public function __construct(RequestStack $requestStack, ProductRepository $productRepo, PaginatorInterface $paginator)
    {
        $this->requestStack = $requestStack;
        $this->productRepo = $productRepo;
        $this->paginator = $paginator;

    }

    public function getPaginatedProducts(int $limit = 2)
    {
        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt('page', 1);

        $productsQuery = $this->productRepo->findForPagination();

        return $this->paginator->paginate($productsQuery,$page,$limit);
    }
}