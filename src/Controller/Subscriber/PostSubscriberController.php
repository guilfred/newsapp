<?php

namespace App\Controller\Subscriber;

use App\Entity\Subscriber;
use App\Exception\ApiResourceException;
use App\Repository\SubscriberRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PostSubscriberController extends AbstractController
{
    public function __invoke(Request $request, SubscriberRepository $subscriberRepository, LoggerInterface $logger): Response
    {
        try {
            $data = json_decode($request->getContent(), true);

            $subscriberRepository->createSubscriber($data['email']);

            return $this->json(
                ['status' => 'done', 'message' => Subscriber::SUBSCRIBE_SUCCESS],
                Response::HTTP_CREATED,
                ['Content-Type' => 'application/json']
            );
        }
        catch (ApiResourceException $e) {
            $logger->error($e->getMessage(), ['exception' => $e]);

            return $this->json(
                ['status' => 'failed', 'message' => $e->getMessage()],
                Response::HTTP_OK, // Eviter un 400 ou 500 pour se baser sur le status failed
                ['Content-Type' => 'application/json']
            );
        }
    }
}
