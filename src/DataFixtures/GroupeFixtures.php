<?php

namespace App\DataFixtures;

use App\Entity\Groupe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class GroupeFixtures extends Fixture implements FixtureGroupInterface
{
    const GROUPES = [
        [
            'title' => 'Super administrateur',
            'role' => 'ROLE_SUPER_ADMIN'
        ],
        [
            'title' => 'Administrateur',
            'role' => 'ROLE_ADMIN'
        ]
    ];

    /**
     * @param ObjectManager $manager
     *
     * @return void
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < count(self::GROUPES); $i++) {
            $groupe = new Groupe();
            $groupe
                ->setTitle(self::GROUPES[$i]['title'])
                ->setRole(self::GROUPES[$i]['role'])
            ;
            $manager->persist($groupe);
        }
        $manager->flush();
    }

    /**
     * @return string[]
     */
    public static function getGroups(): array
    {
        return ['groupe'];
    }
}
