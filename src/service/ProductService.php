<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    public function getPaginatedProducts()
    {
        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 5);
        if ($page < 1) {
            throw new BadRequestHttpException('Le numéro de page doit être supérieur ou égal à 1.');
        }


        $productsQuery = $this->productRepo->findForPagination();

        return $this->paginator->paginate($productsQuery,$page,$limit);
    }
}