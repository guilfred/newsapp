<?php

namespace App\Controller\Subscriber;

use App\Entity\Subscriber;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UnsubscribeAction extends AbstractController
{
    /**
     * @param Request         $request
     * @param ManagerRegistry $managerRegistry
     *
     * @return Response
     */
    public function __invoke(Request $request, ManagerRegistry $managerRegistry): Response
    {
        $token = $request->query->get('token');
        $action = $request->query->get('unsubscribe');

        $em = $managerRegistry->getManager();
        $subscriber = $em->getRepository(Subscriber::class)->findOneBy(['tokenID' => $token]);

        if ($subscriber && $action) {
            $subscriber
                ->setEnabled(false)
                ->setSubscribAt(new \DateTimeImmutable())
            ;
            $em->flush();

            return new Response('success');
        }

        return new Response('fail');
    }
}
