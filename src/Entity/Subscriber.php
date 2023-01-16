<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\Subscriber\UnsubscribeAction;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity('email')]
#[ORM\Entity(repositoryClass: SubscriberRepository::class)]
#[ApiResource(
    operations: [
        new Post(
            denormalizationContext: ['groups' => ['Post:Subscriber']]
        ),
        new GetCollection(),
        new Get(),
        new Get(
            uriTemplate: '/unsubscribe',
            controller: UnsubscribeAction::class,
            read: false,
            name: 'unsubscribe'
        )
    ],
)]
class Subscriber
{
    const TOKEN_LENGTH = 50;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups(['Post:Subscriber'])]
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $subscribAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $unsubscribAt = null;

    #[ORM\Column(length: 255)]
    private ?string $tokenID = null;

    #[ORM\Column]
    private ?bool $enabled = true;

    public function __construct()
    {
        $this->subscribAt = new \DateTimeImmutable();
        $this->tokenID = $this->randomToken(self::TOKEN_LENGTH);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSubscribAt(): ?\DateTimeImmutable
    {
        return $this->subscribAt;
    }

    public function setSubscribAt(\DateTimeImmutable $subscribAt): self
    {
        $this->subscribAt = $subscribAt;

        return $this;
    }

    public function getUnsubscribAt(): ?\DateTimeImmutable
    {
        return $this->unsubscribAt;
    }

    public function setUnsubscribAt(?\DateTimeImmutable $unsubscribAt): self
    {
        $this->unsubscribAt = $unsubscribAt;

        return $this;
    }

    public function getTokenID(): ?string
    {
        return $this->tokenID;
    }

    public function setTokenID(?string $tokenID): self
    {
        $this->tokenID = $tokenID;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @param int $length
     *
     * @return string
     */
    private function randomToken(int $length): string
    {
        try {
            return bin2hex(random_bytes($length));
        } catch (\Exception $e) {
            $alphabet = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN";

            return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
        }
    }
}