<?php

namespace App\Controller\Subscriber;

use App\Entity\Subscriber;
use App\Repository\SubscriberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UnsubscribeAction extends AbstractController
{
    /**
     * @param Request              $request
     * @param SubscriberRepository $subscriberRepository
     *
     * @return Response
     */
    public function __invoke(Request $request, SubscriberRepository $subscriberRepository): Response
    {
        $token = $request->query->get('token');
        $action = $request->query->get('action');

        if ($action !== 'unsubscribe') {
            return new Response(Subscriber::UNSUBSCRIBE_FAILED);
        }

        $subscriber = $subscriberRepository->findOneBy(['tokenID' => $token]);
        if (!$subscriber) {
            return new Response(Subscriber::UNSUBSCRIBE_FAILED);
        }

        $subscriberRepository->unsubscribe($subscriber);

        return new Response(Subscriber::UNSUBSCRIBE_SUCCESS);
    }
}
