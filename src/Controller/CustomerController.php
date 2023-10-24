<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class CustomerController extends AbstractController
{
    /**
     * This method allows you to recover all the customers.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return the list of customers",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Customer::class))
     *     )
     * )
     * )
     * @OA\Tag(name="Customers")
     *
     * @param CustomerRepository $customerRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/customers', name: 'customers', methods: ['GET'])]
    public function getCustomerList(CustomerRepository $customerRepository, SerializerInterface $serializer): JsonResponse
    {
        $customerList = $customerRepository->findAll();
        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonCustomerList = $serializer->serialize($customerList, 'json', $context);
        
        return new JsonResponse($jsonCustomerList, Response::HTTP_OK, [], true);
    }

        /**
     * this method returns the detail of a customer.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return the detail of a customer",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Customer::class))
     *     )
     * )
     * )
     * @OA\Tag(name="Customers")
     *
     * @param Customer $customer
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/customers/{id}', name: 'detailCustomer', methods: ['GET'])]
    public function getDetailcustomer(Customer $customer, SerializerInterface $serializer) : JsonResponse 
    {
        $context = SerializationContext::create()->setGroups(['getCustomers', 'getUsers']);
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);

        return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
    }
}
