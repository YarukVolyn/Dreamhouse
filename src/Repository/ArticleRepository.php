<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method null|Article find($id, $lockMode = null, $lockVersion = null)
 * @method null|Article findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    private WorkflowInterface $articlePublishingStateMachine;

    public function __construct(ManagerRegistry $registry, WorkflowInterface $articlePublishingStateMachine)
    {
        $this->articlePublishingStateMachine = $articlePublishingStateMachine;

        parent::__construct($registry, Article::class);
    }

    public function add(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Article $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function draft(Article $entity): void
    {
        try {
            $this->articlePublishingStateMachine->apply($entity, 'draft');
        } catch (LogicException $exception) {
        }
    }

    public function toDraft(Article $entity): void
    {
        try {
            $this->articlePublishingStateMachine->apply($entity, 'to_draft');
        } catch (LogicException $exception) {
        }
    }

    public function review(Article $entity): void
    {
        try {
            $this->articlePublishingStateMachine->apply($entity, 'review');
        } catch (LogicException $exception) {
        }
    }

    public function publish(Article $entity): void
    {
        try {
            $this->articlePublishingStateMachine->apply($entity, 'publish');
        } catch (LogicException $exception) {

        }
    }

    public function rejected(Article $entity): void
    {
        try {
            $this->articlePublishingStateMachine->apply($entity, 'reject');
        } catch (LogicException $exception) {
        }
    }

    /**
     * @param mixed $value
     *
     * @return Article[] Returns an array of Article objects to review
     */
    public function findDraftArticle($value = 'draft'): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.currentPlace = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param mixed $value
     *
     * @return Article[] Returns an array of Article objects to review
     */
    public function findArticleToReview($value = 'to_review'): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.currentPlace = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param mixed $value
     *
     * @return Article[] Returns an array of Article objects to review
     */
    public function findRejectedArticle($value = 'rejected'): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.currentPlace = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    /**
     * @param mixed $value
     *
     * @return Article[] Returns an array of Article objects to review
     */
    public function findPublishedArticle($value = 'published'): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.currentPlace = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

//    /**
//     * @return Article[] Returns an array of Article objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Article
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
