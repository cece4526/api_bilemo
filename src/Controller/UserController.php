<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use App\Service\JWTService;
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
use  Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;

class UserController extends AbstractController
{
    /**
     * This method allows you to recover all the users.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return the list of users",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *     )
     * )
     * )
     * @OA\Tag(name="Users")
     *
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/users', name: 'users', methods: ['GET'])]
    public function getUserList(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $userList = $userRepository->findAll();
        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonUserList = $serializer->serialize($userList, 'json', $context);
        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }

    /**
     * this method returns the detail of a user.
     *
     * @OA\Response(
     *     response=200,
     *     description="Return the detail of a user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *     )
     * )
     * )
     * @OA\Tag(name="Users")
     *
     * @param User $user
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    #[Route('/api/users/{id}', name: 'detailUser', methods: ['GET'])]
    public function getDetailUser(User $user, SerializerInterface $serializer) : JsonResponse 
    {

        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonUser = $serializer->serialize($user, 'json', $context);
        return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
    }

    /**
     * this method delete a user.
     *
     * @OA\Response(
     *     response=204,
     *     description="Return null",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *     )
     * )
     * )
     * @OA\Tag(name="Users")
     *
     * @param User $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/api/users/{id}', name: 'deleteUser', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour supprimer cette utilisateur')]
    public function deleteUser(User $user, EntityManagerInterface $em): JsonResponse 
    {
        $em->remove($user);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * this method create a user.
     *
     * @OA\Response(
     *     response=201,
     *     description="Return user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *     )
     * )
     * )
     * @OA\Tag(name="Users")
     *
     * @param User $user
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */    
    #[Route('/api/users', name: 'createUser', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour créer un utilisateur')]
    public function createUser(ValidatorInterface $validator,UserPasswordHasherInterface $userPasswordHasher, CustomerRepository $customerRepository ,Request $request, EntityManagerInterface $em, SerializerInterface $serializer, UrlGeneratorInterface $urlGenerator, JWTService $jwtService): JsonResponse 
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $password = $user->getPassword();
        $user->setPassword($userPasswordHasher->hashPassword($user, $password));
        $token = $request->headers->get('authorization');
        $payload = $jwtService->getPayload($token);
        $customer = $customerRepository->findOneByEmail($payload["username"]);
        $user->setRoles(["ROLE_USER"]);
        $user->setCustomer($customer);

        $em->persist($user);
        $em->flush();

        $context = SerializationContext::create()->setGroups(['getUsers']);
        $jsonUser = $serializer->serialize($user, 'json', $context);

        $location = $urlGenerator->generate('detailUser', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["location" => $location],true);
    }

    /**
     * this method modified a user.
     *
     * @OA\Response(
     *     response=204,
     *     description="Return null",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *     )
     * )
     * )
     * @OA\Tag(name="Users")
     *
     * @param User $currentUser
     * @param ValidatorInterface $validator
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */   
    #[Route('/api/users/{id}', name:"updateUser", methods:['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour modifier cette utilisateur')]
    public function updateUser(ValidatorInterface $validator,UserPasswordHasherInterface $userPasswordHasher, Request $request, SerializerInterface $serializer, User $currentUser, EntityManagerInterface $em): JsonResponse 
    {
        $newUser = $serializer->deserialize($request->getContent(), User::class, 'json');

        $currentUser->setEmail($newUser->getEmail());
        $currentUser->setFirstname($newUser->getFirstname());
        $currentUser->setLastname($newUser->getLastname());
        $plainPassword = $newUser->getPassword();
        if ($plainPassword !== null) {
            if ($userPasswordHasher->isPasswordValid($currentUser, $plainPassword)) {
            }
            else {
                $currentUser->setPassword($userPasswordHasher->hashPassword($newUser, $newUser->getPassword()));
            }
        }

        $errors = $validator->validate($currentUser);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $em->persist($currentUser);
        $em->flush();
    
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
   }
}
