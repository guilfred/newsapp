<?php

namespace App\DataFixtures;

use App\Entity\Groupe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    const USERNAMES = [
        'sheperd@mail.test',
        'mia@mail.test',
        'customer@mail.test'
    ];

    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < count(self::USERNAMES); $i++) {
            $groupe = $manager->getRepository(Groupe::class)->findOneBy(['role' => GroupeFixtures::GROUPES[$i]['role']]);
            $user = $this->createUser(self::USERNAMES[$i], $groupe);
            $manager->persist($user);
        }

        $manager->flush();
    }

    /**
     * @param string $email
     * @param Groupe $groupe
     *
     * @return User
     */
    private function createUser(string $email, Groupe $groupe): User
    {
        $user = new User();
        $user
            ->setEmail($email)
            ->setGroupe($groupe)
            ->addRole($groupe->getRole())
            ->setPassword($this->passwordHasher->hashPassword($user, 'P@ssw0rd'))
        ;

        return $user;
    }


    /**
     * @return string[]
     */
    public static function getGroups(): array
    {
        return ['user'];
    }
}
