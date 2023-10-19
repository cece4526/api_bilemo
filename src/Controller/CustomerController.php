<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomerController extends AbstractController
{
    #[Route('/api/customers', name: 'customers', methods: ['GET'])]
    public function getCustomerList(CustomerRepository $customerRepository, SerializerInterface $serializer): JsonResponse
    {
        $customerList = $customerRepository->findAll();
        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonCustomerList = $serializer->serialize($customerList, 'json', $context);
        
        return new JsonResponse($jsonCustomerList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/customers/{id}', name: 'detailCustomer', methods: ['GET'])]
    public function getDetailcustomer(Customer $customer, SerializerInterface $serializer) : JsonResponse 
    {
        $context = SerializationContext::create()->setGroups(['getCustomers', 'getUsers']);
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);

        return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
    }
}
