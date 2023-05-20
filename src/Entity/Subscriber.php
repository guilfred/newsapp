<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\Subscriber\PostSubscriberController;
use App\Controller\Subscriber\UnsubscribeController;
use App\Repository\SubscriberRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: 'email', message: "Cette adresse mail existe déjà !")]
#[ORM\Entity(repositoryClass: SubscriberRepository::class)]
#[ApiFilter(SearchFilter::class, properties: ['enabled', 'email' => 'partial'])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'subscribAt'], arguments: ['orderParameterName' => 'ord'])]
#[ApiResource(
    operations: [
        new Post(
            uriTemplate: '/subscribers',
            controller: PostSubscriberController::class,
            normalizationContext: ['groups' => ['Post:Read:Subscriber']],
            denormalizationContext: ['groups' => ['Post:Subscriber']],
            read: false,
            name: 'post_subscriber'
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['Read:Subscriber']],
            security: "is_granted('ROLE_SUPER_ADMIN')"
        ),
        new Get(
            security: "is_granted('ROLE_ADMIN')"
        ),
        new Get(
            uriTemplate: '/unsubscribe',
            controller: UnsubscribeController::class,
            read: false,
            name: 'unsubscribe'
        )
    ],
)]
class Subscriber
{
    const TOKEN_LENGTH = 50;
    const UNSUBSCRIBE_FAILED = 'Unsubscribe failed !';
    const UNSUBSCRIBE_SUCCESS = 'Unsubscribe successfully !';
    const SUBSCRIBE_SUCCESS = 'Votre inscription a bien été effectuée.';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[Groups(['Read:Subscriber'])]
    #[ORM\Column]
    private ?int $id = null;

    #[Groups([
        'Post:Subscriber',
        'Post:Read:Subscriber',
        'Read:Subscriber'
    ])]
    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\Email(message: "L'adresse {{ value }} n'est pas valide !")]
    private ?string $email = null;

    #[Groups([
        'Read:Subscriber',
        'Post:Read:Subscriber'
    ])]
    #[ORM\Column]
    private ?\DateTimeImmutable $subscribAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $unsubscribAt = null;

    // Utile pour la désincription à la newsletter
    #[ORM\Column(length: 255)]
    private ?string $tokenID = null;

    #[Groups(['Read:Subscriber'])]
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
