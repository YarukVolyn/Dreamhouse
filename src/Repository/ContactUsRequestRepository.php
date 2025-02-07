<?php

namespace App\Repository;

use App\Entity\ContactUsRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactUsRequest>
 *
 * @method ContactUsRequest|null find($id, $lockMode = null, $lockVersion = null)
 * @method ContactUsRequest|null findOneBy(array $criteria, array $orderBy = null)
 * @method ContactUsRequest[]    findAll()
 * @method ContactUsRequest[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactUsRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactUsRequest::class);
    }

    public function add(ContactUsRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(ContactUsRequest $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

}
