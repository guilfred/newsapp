<?php

namespace App\Service;

use App\Entity\Archived;
use App\Entity\Subscriber;
use Doctrine\ORM\EntityManagerInterface;

class ArchivedSubscriber
{
    public function __construct(private readonly EntityManagerInterface $em) {}

    public function archived(): void
    {
        $subscribers = $this->em->getRepository(Subscriber::class)->findBy(['enabled' => false]);
        foreach ($subscribers as $subscriber) {
            $archived = new Archived($subscriber->getEmail());
            $this->em->persist($archived);

            $this->em->remove($subscriber);
        }

        $this->em->flush();
    }


}
