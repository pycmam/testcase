<?php

namespace App\DataFixtures;

use App\Entity\Account;
use App\Entity\Operation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AccountFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        foreach (range(1,5) as $n)
        {
            $account = new Account();
            $account->setUsername("User {$n}");

            $manager->persist($account);

            foreach (range(1, random_int(1, 5)) as $i)
            {
                $operation = new Operation();
                $operation->setAccount($account);
                $operation->setCreated(new \DateTime());
                $operation->setAmount(random_int(1000, 5000));

                $manager->persist($operation);
            }
        }

        $manager->flush();
    }
}
