<?php

namespace App\Controller\Subscriber;

use App\Entity\Subscriber;
use App\Repository\SubscriberRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UnsubscribeController extends AbstractController
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

        if (!isset($token) || !isset($action) || $action !== 'unsubscribe') {
            return $this->json(
                ['status' => 'failed', 'message' => Subscriber::UNSUBSCRIBE_FAILED],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }

        $subscriber = $subscriberRepository->findOneBy(['tokenID' => $token]);
        if (!$subscriber) {
            return $this->json(
                ['status' => 'failed', 'message' => Subscriber::UNSUBSCRIBE_FAILED],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type' => 'application/json']
            );
        }

        $subscriberRepository->unsubscribe($subscriber);

        return $this->json(
            ['status' => 'done', 'message' => Subscriber::UNSUBSCRIBE_SUCCESS],
            Response::HTTP_OK,
            ['Content-Type' => 'application/json']
        );
    }
}
