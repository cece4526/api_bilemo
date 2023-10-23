<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {

            $customer = new Customer();
            $customer->setName('entreprise');
            $customer->setRoles(["ROLE_ADMIN"]);
            $customer->setEmail('customer@customer.fr');
            $customer->setPassword($this->userPasswordHasher->hashPassword($customer, "password"));
            $manager->persist($customer);
            
        for ($i=0; $i < 10; $i++) { 
            $user = new User();
            $user->setFirstname('user'.$i);
            $user->setLastname('lastname'.$i);
            $user->setRoles(["ROLE_USER"]);
            $user->setEmail('user'.$i.'@user.fr');
            $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
            $user->setCustomer($customer);
            $manager->persist($user);

        }
        for ($i=0; $i < 10; $i++) { 
            $product = new Product();
            $product->setBrand('marque'. $i);
            $product->setModel('model'. $i);
            $product->setPrice('prix'. $i);
            $product->setColor('couleur'. $i);
            $product->setDescription('description du smartphone n¤'. $i);
            $manager->persist($product);
        }
        

        $manager->flush();
    }
}
