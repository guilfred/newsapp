<?php

namespace App\Repository;

use App\Entity\Subscriber;
use App\Exception\ApiResourceException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @extends ServiceEntityRepository<Subscriber>
 *
 * @method Subscriber|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscriber|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscriber[]    findAll()
 * @method Subscriber[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriberRepository extends ServiceEntityRepository
{

    public function __construct(
        ManagerRegistry $registry,
        private readonly ValidatorInterface $validator,
        private readonly LoggerInterface $logger
    )
    {
        parent::__construct($registry, Subscriber::class);
    }

    public function save(Subscriber $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Subscriber $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Handle unsubscribe
     *
     * @param Subscriber $subscriber
     *
     * @return void
     */
    public function unsubscribe(Subscriber $subscriber): void
    {
        if (!$subscriber->isEnabled() && !is_null($subscriber->getUnsubscribAt())) {
            return;
        }

        $subscriber
            ->setEnabled(false)
            ->setUnsubscribAt(new \DateTimeImmutable())
        ;

        $this->_em->flush();
    }

    /**
     * @param string $email
     *
     * @return void
     *
     * @throws ApiResourceException
     */
    public function createSubscriber(string $email): void
    {
        $subscriber = new Subscriber();
        $subscriber->setEmail($email);

        $dataErrors = $this->validateSubscriber($subscriber);
        if (!empty($dataErrors)) {
            throw new ApiResourceException(implode(', ', $dataErrors));
        }

        $this->save($subscriber, true);
    }

    /**
     * @param Subscriber $subscriber
     *
     * @return array
     */
    private function validateSubscriber(Subscriber $subscriber): array
    {
        $errors = $this->validator->validate($subscriber);
        $dataErrors = [];
        foreach ($errors as $error) {
            $dataErrors[$error->getPropertyPath()] = $error->getMessage();
        }

        return $dataErrors;
    }


}
