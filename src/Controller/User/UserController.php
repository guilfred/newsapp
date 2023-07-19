<?php

namespace App\Controller\User;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractController
{
    /**
     * @return Response
     */
    public function __invoke(): Response
    {
        $user = $this->getUser();
        $data = $user instanceof User ? $this->getUserDataInfo($user): [];

        return $this->json(['data' => $data], Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }


    /**
     * @param User $user
     *
     * @return array
     */
    private function getUserDataInfo(User $user): array
    {
        return [
            'email' => $user->getEmail(),
            'groupe' => $user->getGroupe()->getTitle(),
            'lastLoginAt' => $user->getLastLogin() ?? $user->getLastLogin()->format('d-m-Y')
        ];
    }
}
